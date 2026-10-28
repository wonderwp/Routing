<?php

declare(strict_types=1);

namespace {
    if (!function_exists('add_action')) {
        function add_action($hook, $callback, $priority = 10, $accepted_args = 1)
        {
            $GLOBALS['wwp_test_actions'][$hook][] = compact('callback', 'priority', 'accepted_args');

            return true;
        }
    }

    if (!function_exists('add_filter')) {
        function add_filter($hook, $callback, $priority = 10, $accepted_args = 1)
        {
            $GLOBALS['wwp_test_filters'][$hook][] = compact('callback', 'priority', 'accepted_args');

            return true;
        }
    }

    if (!function_exists('sanitize_title')) {
        function sanitize_title($title, $fallback_title = '', $context = 'save')
        {
            $title = strtolower((string) $title);

            return preg_replace('/[^a-z0-9]+/', '-', $title) ?: $fallback_title;
        }
    }

    if (!function_exists('get_locale')) {
        function get_locale()
        {
            return $GLOBALS['wwp_test_locale'] ?? 'en_US';
        }
    }

    if (!function_exists('get_bloginfo')) {
        function get_bloginfo($show = '', $filter = 'raw')
        {
            if ($show === 'url') {
                return 'https://example.test';
            }

            return '';
        }
    }

    if (!function_exists('wp_redirect')) {
        function wp_redirect($location, $status = 302)
        {
            $GLOBALS['wwp_test_redirects'][] = ['location' => $location, 'status' => $status];

            return false;
        }
    }

    if (!function_exists('add_rewrite_tag')) {
        function add_rewrite_tag($tag, $regex, $query = '')
        {
            return true;
        }
    }

    if (!function_exists('add_rewrite_rule')) {
        function add_rewrite_rule($regex, $query, $after = 'bottom')
        {
            return true;
        }
    }

    if (!class_exists('WP_Error', false)) {
        class WP_Error
        {
            /** @var array<string, string[]> */
            private array $errors = [];

            public function __construct(?string $code = '', string $message = '', $data = '')
            {
                if ($code !== null && $code !== '') {
                    $this->errors[$code][] = $message;
                }
            }

            public function get_error_codes(): array
            {
                return array_keys($this->errors);
            }
        }
    }

    if (!class_exists('WP', false)) {
        class WP
        {
            /** @var array<string, mixed> */
            public array $query_vars = [];
        }
    }
}
