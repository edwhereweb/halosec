<?php

declare(strict_types=1);

namespace HaloSec\Controllers;

use HaloSec\Models\Lead;
use HaloSec\Security\CsrfManager;
use HaloSec\Services\LeadService;
use HaloSec\View;

/**
 * Renders and processes every lead-capture form (audit, enquiry, consultation, emergency, contact).
 */
final class FormController
{
    /**
     * @param array<string, array<string, mixed>> $services
     * @param array<string, mixed> $session
     */
    public function __construct(
        private View $view,
        private LeadService $leads,
        private CsrfManager $csrf,
        private array $services,
        private array &$session,
        private ?\HaloSec\Security\LoginThrottle $throttle = null,
        private string $clientId = '',
    ) {
    }

    /**
     * @param array<string, mixed> $post
     */
    public function handle(
        string $type,
        string $path,
        string $title,
        string $template,
        string $method,
        array $post,
        ?string $slug = null,
        string $description = '',
        bool $noindex = false,
    ): string|int
    {
        $data = [
            'title' => $title,
            'path' => $path,
            'formAction' => $path,
            'formType' => $type,
            'old' => [],
            'errors' => [],
            'flash' => null,
            'service' => null,
            'slug' => $slug,
            'description' => $description,
            'noindex' => $noindex,
        ];

        if ($slug !== null) {
            $service = $this->services[$slug] ?? null;
            if ($service === null) {
                return 404;
            }
            $data['service'] = $service;
            $data['title'] = (string) ($service['seo_title'] ?? $service['title']);
            $data['description'] = (string) ($service['meta_description'] ?? $service['short'] ?? '');
            // The canonical URL must be the service page itself, never /services.
            $data['path'] = '/services/' . $slug;
        }

        if ($method === 'POST') {
            if ($this->throttle !== null && $this->throttle->isLocked($this->clientId)) {
                http_response_code(429);
                header('Retry-After: 60');
                $data['old'] = $post;
                $data['flash'] = ['type' => 'error', 'message' => 'Too many requests. Please wait a moment before trying again or call our emergency line.'];
            } elseif (!$this->csrf->isValid($post[CsrfManager::FIELD] ?? null)) {
                http_response_code(419);
                $data['old'] = $post;
                $data['flash'] = ['type' => 'error', 'message' => 'Your session expired. Please try submitting again.'];
            } else {
                if ($type === Lead::TYPE_ENQUIRY) {
                    $post['service'] = $slug;
                }
                $outcome = $this->leads->submit($type, $post);
                if ($outcome['status'] === 'ok') {
                    $this->csrf->rotate();
                    $this->session['flash'] = $this->successMessage($type);
                    header('Location: ' . $path . '#form-status', true, 303);

                    return '';
                }
                $this->throttle?->recordFailure($this->clientId);
                $data['old'] = $post;
                $data['errors'] = $outcome['result']->errors;
                http_response_code($outcome['status'] === 'invalid' ? 422 : 500);
                $data['flash'] = [
                    'type' => 'error',
                    'message' => $outcome['status'] === 'invalid'
                        ? 'Please correct the highlighted fields and try again.'
                        : 'We could not save your request right now. Please call us or try again shortly.',
                ];
            }
        } elseif (isset($this->session['flash'])) {
            $data['flash'] = ['type' => 'success', 'message' => $this->session['flash']];
            unset($this->session['flash']);
        }

        return $this->view->render($template, $data);
    }

    private function successMessage(string $type): string
    {
        return match ($type) {
            Lead::TYPE_AUDIT => 'Thank you! Your audit request is in. A HaloSec specialist will contact you within one business day.',
            Lead::TYPE_CONSULTATION => 'Thank you! We have received your consultation request and will be in touch shortly.',
            Lead::TYPE_EMERGENCY => 'Incident report received. Our response team has been alerted. If you have not heard from us in 15 minutes, call the emergency hotline.',
            Lead::TYPE_SECURITY_REPORT => 'Security report received. Our response team will review it shortly.',
            Lead::TYPE_NEWSLETTER => 'Thank you for subscribing to HaloSec updates.',
            Lead::TYPE_ENQUIRY => 'Thank you! Your enquiry has been sent. Our team will reply shortly.',
            default => 'Thank you! Your message has been sent.',
        };
    }
}
