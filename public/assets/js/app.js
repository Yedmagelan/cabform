/**
 * CabForm — Global JavaScript (jQuery)
 * Interactions dynamiques, animations scroll, Ajax, toasts, loader
 */
$(document).ready(function() {
    'use strict';

    // ── Page Loader ──────────────────────────────────────────────
    $(window).on('load', function() {
        setTimeout(function() {
            $('.loader-cabform').addClass('hidden');
        }, 500);
    });

    // ── Navbar Scroll Effect ─────────────────────────────────────
    function checkScroll() {
        const $navbar = $('.navbar-cabform');
        if ($(window).scrollTop() > 50) {
            $navbar.addClass('scrolled');
        } else {
            $navbar.removeClass('scrolled');
        }
    }
    $(window).on('scroll', checkScroll);
    checkScroll(); // Run immediately on load

    // ── Scroll Animations (Intersection Observer) ────────────────
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                $(entry.target).addClass('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    $('.fade-in, .fade-in-left, .fade-in-right, .scale-in').each(function() {
        observer.observe(this);
    });

    // ── Counter Animation ────────────────────────────────────────
    function animateCounters() {
        $('.counter').each(function() {
            const $this = $(this);
            if ($this.hasClass('counted')) return;

            const target = parseInt($this.attr('data-target'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            $this.addClass('counted');

            const timer = setInterval(function() {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                $this.text(Math.floor(current).toLocaleString('fr-FR'));
            }, 16);
        });
    }

    // Observer for counters
    const counterObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                animateCounters();
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.counter').forEach(function(el) {
        counterObserver.observe(el);
    });

    // ── Toast Notifications ──────────────────────────────────────
    window.showToast = function(message, type = 'success', duration = 5000) {
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-times-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        const colors = {
            success: '#00d97e',
            error: '#e63757',
            warning: '#f5a623',
            info: '#39afd1'
        };

        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast toast-cabform" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${duration}">
                <div class="toast-header">
                    <i class="fas ${icons[type]} me-2" style="color: ${colors[type]}"></i>
                    <strong class="me-auto">${type === 'success' ? 'Succès' : type === 'error' ? 'Erreur' : type === 'warning' ? 'Attention' : 'Information'}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;

        let $container = $('#toast-container');
        if (!$container.length) {
            $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>');
            $container = $('#toast-container');
        }

        $container.append(toastHtml);
        const toastElement = new bootstrap.Toast(document.getElementById(toastId));
        toastElement.show();

        $(`#${toastId}`).on('hidden.bs.toast', function() {
            $(this).remove();
        });
    };

    // ── AJAX Global Setup ────────────────────────────────────────
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Global Ajax loading state
    $(document).ajaxStart(function() {
        $('#ajax-loader').removeClass('d-none');
    }).ajaxStop(function() {
        $('#ajax-loader').addClass('d-none');
    });

    // ── Smooth Scroll for Anchor Links ───────────────────────────
    $('a[href^="#"]').not('[data-bs-toggle]').on('click', function(e) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 600);
        }
    });

    // ── Back to Top Button ───────────────────────────────────────
    const $backToTop = $('<button id="back-to-top" class="btn btn-cabform btn-cabform-primary" style="position:fixed;bottom:30px;right:30px;z-index:999;width:48px;height:48px;border-radius:50%;display:none;padding:0;font-size:1.2rem;"><i class="fas fa-arrow-up"></i></button>');
    $('body').append($backToTop);

    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 500) {
            $backToTop.fadeIn();
        } else {
            $backToTop.fadeOut();
        }
    });

    $backToTop.on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 600);
    });

    // ── Sidebar Toggle (Admin/Learner) ───────────────────────────
    $('#sidebar-toggle').on('click', function() {
        $('.sidebar-cabform').toggleClass('show');
    });

    // Close sidebar on click outside (mobile)
    $(document).on('click', function(e) {
        if ($(window).width() < 992) {
            if (!$(e.target).closest('.sidebar-cabform, #sidebar-toggle').length) {
                $('.sidebar-cabform').removeClass('show');
            }
        }
    });

    // ── Password Toggle ──────────────────────────────────────────
    $(document).on('click', '.password-toggle', function() {
        const $input = $(this).closest('.input-group').find('input');
        const type = $input.attr('type') === 'password' ? 'text' : 'password';
        $input.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    // ── Dynamic Search (Catalog) ─────────────────────────────────
    let searchTimeout;
    $('#catalog-search').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val();
        searchTimeout = setTimeout(function() {
            if (typeof loadCatalog === 'function') {
                loadCatalog(1, query);
            }
        }, 300);
    });

    // ── File Upload Preview ──────────────────────────────────────
    $(document).on('change', '.custom-file-input', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').text(fileName || 'Choisir un fichier');
    });

    // ── Confirm Delete ───────────────────────────────────────────
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const $form = $(this).closest('form');
        const itemName = $(this).data('name') || 'cet élément';

        if (confirm(`Êtes-vous sûr de vouloir supprimer ${itemName} ? Cette action est irréversible.`)) {
            $form.submit();
        }
    });

    // ── Tooltip Init ─────────────────────────────────────────────
    $('[data-bs-toggle="tooltip"]').each(function() {
        new bootstrap.Tooltip(this);
    });

    // ── Progress bar animation on course player ──────────────────
    $(document).on('click', '.lesson-item:not(.locked)', function() {
        $('.lesson-item').removeClass('active');
        $(this).addClass('active');
    });

    console.log('%c✨ CabForm LMS — Plateforme de Formation', 'color: #4d6bfe; font-size: 16px; font-weight: bold;');
});
