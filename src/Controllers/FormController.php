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
    ) {
    }

    /**
     * @param array<string, mixed> $post
     */
    public function handle(string $type, string $path, string $title, string $template, string $method, array $post, ?string $slug = null): string|int
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
        ];

        if ($slug !== null) {
            $service = $this->services[$slug] ?? null;
            if ($service === null) {
                return 404;
            }
            $data['service'] = $service;
            $data['title'] = (string) $service['title'];
            $data['path'] = '/services';
        }

        if ($method === 'POST') {
            if (!$this->csrf->isValid($post[CsrfManager::FIELD] ?? null)) {
                http_response_code(419);
                $data['old'] = $post;
                $data['flash'] = ['type' => 'error', 'message' => 'Your session expired. Please try submitting again.'];
            } else {
                if ($type === Lead::TYPE_ENQUIRY) {
                    $post['service'] = $slug;
                }
                $outcome = $this->leads->submit($type, $post);
                if ($outcome['status'] === 'ok') {
                    $this->session['flash'] = $this->successMessage($type);
                    header('Location: ' . $path . '#form-status', true, 303);

                    return '';
                }
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
            Lead::TYPE_ENQUIRY => 'Thank you! Your enquiry has been sent. Our team will reply shortly.',
            default => 'Thank you! Your message has been sent.',
        };
    }
}
