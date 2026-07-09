<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request, int $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        
        // Sécuriser l'accès
        if ($quiz->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'explanation' => 'nullable|string',
            'type' => 'required|in:mcq,true_false,short_answer,matching,open_ended,fill_blank',
            'points' => 'required|numeric|min:0.5',
            'answers' => 'nullable|array',
        ]);

        $question = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => $validated['question_text'],
            'explanation' => $validated['explanation'] ?? null,
            'type' => $validated['type'],
            'points' => $validated['points'],
            'sort_order' => $quiz->questions()->count(),
            'is_active' => true,
        ]);

        $this->saveAnswersForQuestion($question, $request->input('answers', []));

        return back()->with('success', 'Question ajoutée avec succès.');
    }

    public function update(Request $request, int $quizId, int $questionId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $question = $quiz->questions()->findOrFail($questionId);

        // Sécuriser l'accès
        if ($quiz->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'required|numeric|min:0.5',
            'answers' => 'nullable|array',
        ]);

        $question->update([
            'question_text' => $validated['question_text'],
            'explanation' => $validated['explanation'] ?? null,
            'points' => $validated['points'],
        ]);

        // Supprimer les anciennes réponses et insérer les nouvelles
        $question->answers()->delete();
        $this->saveAnswersForQuestion($question, $request->input('answers', []));

        return back()->with('success', 'Question mise à jour avec succès.');
    }

    public function reorder(Request $request, int $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        
        if ($quiz->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        $order = $request->input('order');

        if (is_array($order)) {
            foreach ($order as $index => $id) {
                $quiz->questions()->where('id', $id)->update(['sort_order' => $index]);
            }
            return response()->json(['success' => true, 'message' => 'Questions réorganisées.']);
        }

        return response()->json(['success' => false, 'message' => 'Données invalides.'], 400);
    }

    public function duplicate(int $quizId, int $questionId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $question = $quiz->questions()->with('answers')->findOrFail($questionId);

        $newQuestion = $question->replicate();
        $newQuestion->sort_order = $quiz->questions()->count();
        $newQuestion->save();

        foreach ($question->answers as $answer) {
            $newAnswer = $answer->replicate();
            $newAnswer->question_id = $newQuestion->id;
            $newAnswer->save();
        }

        return back()->with('success', 'Question dupliquée avec succès.');
    }

    public function destroy(int $quizId, int $questionId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $question = $quiz->questions()->findOrFail($questionId);
        $question->delete();

        return back()->with('success', 'Question supprimée avec succès.');
    }

    /**
     * Sauvegarder les réponses pour une question selon son type.
     */
    private function saveAnswersForQuestion(Question $question, array $answersData): void
    {
        if (empty($answersData)) {
            return;
        }

        foreach ($answersData as $index => $data) {
            if ($question->type === 'matching') {
                // Pour l'appariement : key est le texte de gauche, value est la réponse correspondante à droite
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => json_encode([
                        'left' => $data['left'] ?? '',
                        'right' => $data['right'] ?? ''
                    ]),
                    'is_correct' => true,
                    'sort_order' => $index,
                ]);
            } else {
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $data['text'] ?? '',
                    'is_correct' => isset($data['is_correct']) && ($data['is_correct'] == 1 || $data['is_correct'] === true),
                    'feedback' => $data['feedback'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }
    }
}
