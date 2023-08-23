<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;

class MaxFileSizeMiddleware implements MiddlewareInterface
{

    public function process(callable $next)
    {
        $uploadMaxSize = ini_get('upload_max_filesize');
        $postMaxSize = ini_get('post_max_size');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_SERVER['CONTENT_LENGTH'] > $postMaxSize) {
                dd("File size exceeds the maximum allowed ($uploadMaxSize).");
                // You can add a link back to the upload form or an error page.
                exit;
            }
        }

        $next();
    }
}
