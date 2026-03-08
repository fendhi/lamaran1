<?php
// includes/mailer.php
function mail_with_attachment(array $mailConfig, string $to, string $subject, string $textBody, ?string $attachmentPath = null, ?string $attachmentName = null, ?string $attachmentMime = null, ?string $cc = null): bool
{
    $fromEmail = $mailConfig['from_email'] ?? 'no-reply@localhost';
    $fromName = $mailConfig['from_name'] ?? 'Website';

    $headers = [];
    $headers[] = 'From: ' . sprintf('%s <%s>', mb_encode_mimeheader($fromName, 'UTF-8'), $fromEmail);
    $headers[] = 'MIME-Version: 1.0';

    if ($cc) {
        $headers[] = 'Cc: ' . $cc;
    }

    if ($attachmentPath && is_file($attachmentPath)) {
        $boundary = '==Multipart_Boundary_x' . bin2hex(random_bytes(8)) . 'x';
        $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

        $body = '';
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $textBody . "\r\n\r\n";

        $fileContents = file_get_contents($attachmentPath);
        if ($fileContents === false) {
            return false;
        }

        $attachmentName = $attachmentName ?: basename($attachmentPath);
        $attachmentMime = $attachmentMime ?: 'application/octet-stream';
        $encoded = chunk_split(base64_encode($fileContents));

        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: {$attachmentMime}; name=\"{$attachmentName}\"\r\n";
        $body .= "Content-Disposition: attachment; filename=\"{$attachmentName}\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= $encoded . "\r\n";
        $body .= "--{$boundary}--\r\n";

        return mail($to, $subject, $body, implode("\r\n", $headers));
    }

    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    return mail($to, $subject, $textBody, implode("\r\n", $headers));
}

function send_application_email(array $config, array $applicant, array $cv): bool
{
    $to = (string)$config['mail']['to'];
    $subject = 'Lamaran Baru - ' . ($applicant['full_name'] ?? '-') . ' - ' . ($applicant['position_applied'] ?? '-');

    $lines = [];
    $lines[] = 'Ada lamaran baru masuk via website:';
    $lines[] = '';
    $lines[] = 'Nama: ' . ($applicant['full_name'] ?? '');
    $lines[] = 'Email: ' . ($applicant['email'] ?? '');
    $lines[] = 'No HP: ' . ($applicant['phone'] ?? '');
    $lines[] = 'Posisi: ' . ($applicant['position_applied'] ?? '');
    $lines[] = 'Alamat: ' . ($applicant['address'] ?? '');
    $lines[] = '';
    $lines[] = 'Cover Letter:';
    $lines[] = (string)($applicant['cover_letter'] ?? '');
    $lines[] = '';
    $lines[] = 'CV terlampir.';

    $cc = null;
    if (!empty($config['mail']['cc_applicant'])) {
        $cc = (string)($applicant['email'] ?? '');
        if ($cc === '') {
            $cc = null;
        }
    }

    return mail_with_attachment(
        $config['mail'],
        $to,
        $subject,
        implode("\n", $lines),
        $cv['absolute_path'] ?? null,
        $cv['original_name'] ?? null,
        $cv['mime'] ?? null,
        $cc
    );
}
