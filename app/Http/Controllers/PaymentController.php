<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Course;
use App\Services\CinetPayService;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(
        protected CinetPayService $cinetPayService,
        protected EnrollmentService $enrollmentService,
    ) {}

    /**
     * Initier le paiement pour un cours.
     */
    public function initiate(Request $request, string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        // Vérifier si déjà inscrit
        if ($user->enrolledIn($course)) {
            return redirect()->route('learner.course.player', $course->slug)
                ->with('info', 'Vous êtes déjà inscrit à cette formation.');
        }

        // Créer la commande
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'subtotal' => $course->price,
            'total' => $course->price,
            'currency' => 'XOF',
            'status' => 'pending',
        ]);

        $order->items()->create([
            'course_id' => $course->id,
            'quantity' => 1,
            'price' => $course->price,
            'total' => $course->price,
        ]);

        $transactionId = 'TXN-' . $order->id . '-' . time();

        // Créer le paiement
        $payment = Payment::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'amount' => $order->total,
            'currency' => 'XOF',
            'channel' => 'cinetpay',
            'status' => 'pending',
        ]);

        // Initier le paiement CinetPay
        $result = $this->cinetPayService->initPayment([
            'transaction_id' => $transactionId,
            'amount' => intval($order->total),
            'currency' => 'XOF',
            'description' => "Inscription: {$course->title}",
            'customer_name' => $user->first_name,
            'customer_surname' => $user->last_name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone ?? '',
            'metadata' => [
                'order_id' => $order->id,
                'course_id' => $course->id,
                'user_id' => $user->id,
            ],
        ]);

        if ($result['success']) {
            return redirect($result['payment_url']);
        }

        $payment->update(['status' => 'failed']);
        $order->update(['status' => 'failed']);

        return back()->with('error', 'Erreur lors de l\'initialisation du paiement : ' . ($result['error'] ?? 'Veuillez réessayer.'));
    }

    /**
     * Webhook IPN CinetPay (notification de paiement).
     */
    public function notify(Request $request)
    {
        // Sécurité : Validation de la signature du Webhook IPN
        if (!$this->cinetPayService->isValidSignature($request)) {
            \Illuminate\Support\Facades\Log::warning('Tentative de webhook CinetPay invalide ou non signée');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $transactionId = $request->input('cpm_trans_id');

        if (!$transactionId) {
            return response()->json(['error' => 'Transaction ID missing'], 400);
        }

        $payment = Payment::where('transaction_id', $transactionId)->first();
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Idempotence : Éviter de traiter deux fois le même webhook
        if ($payment->status === 'completed') {
            return response()->json(['success' => true, 'message' => 'Payment already processed']);
        }

        $result = $this->cinetPayService->verifyTransaction($transactionId);

        if ($result['success'] && $result['status'] === 'ACCEPTED') {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'payment_method' => $result['payment_method'] ?? 'cinetpay',
                'gateway_response' => $result['data'] ?? [],
            ]);

            $payment->order->update(['status' => 'paid']);

            // Activer le compte de l'utilisateur s'il est en attente
            if ($payment->user->status === 'pending') {
                $payment->user->update(['status' => 'active']);
            }

            // Activer l'inscription pour chaque cours de la commande
            foreach ($payment->order->items as $item) {
                if ($item->course_id) {
                    $this->enrollmentService->enroll(
                        $payment->user,
                        $item->course,
                        $payment->order_id
                    );
                }
            }

            // Déclencher l'événement de paiement reçu
            event(new \App\Events\PaymentReceived($payment));

            return response()->json(['success' => true]);
        }

        $payment->update([
            'status' => 'failed',
            'gateway_response' => $result['data'] ?? [],
        ]);
        $payment->order->update(['status' => 'failed']);

        return response()->json(['status' => 'failed']);
    }

    /**
     * Retour après paiement.
     */
    public function return(Request $request)
    {
        $transactionId = $request->input('transaction_id') ?? $request->input('cpm_trans_id');
        if ($transactionId) {
            $payment = Payment::where('transaction_id', $transactionId)->first();
            if ($payment) {
                return redirect()->route('payment.success', $payment->order_id);
            }
        }
        return redirect()->route('learner.dashboard')->with('success', 'Paiement traité avec succès ! Vous pouvez maintenant accéder à votre formation.');
    }

    /**
     * Annulation de paiement.
     */
    public function cancel()
    {
        return redirect()->route('catalog')->with('error', 'Le paiement a été annulé. Vous pouvez réessayer.');
    }

    /**
     * Afficher la page de checkout.
     */
    public function checkout(string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        if ($user->enrolledIn($course)) {
            return redirect()->route('learner.course.player', $course->slug)
                ->with('info', 'Vous êtes déjà inscrit à cette formation.');
        }

        return view('learner.checkout.checkout', compact('course', 'user'));
    }

    /**
     * Page de succès de paiement.
     */
    public function success(int $orderId)
    {
        $order = Order::with('items.course')->findOrFail($orderId);
        return view('learner.checkout.success', compact('order'));
    }
}
