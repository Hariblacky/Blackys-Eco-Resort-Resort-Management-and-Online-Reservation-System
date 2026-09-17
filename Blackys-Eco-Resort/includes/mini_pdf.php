<?php
/**
 * MiniPDF — a tiny, dependency-free PDF writer.
 *
 * Supports just what an invoice needs: text (with basic bold/regular
 * Helvetica), straight lines and filled rectangles, on a single A4 page.
 * No Composer / external library required, so booking invoices work on
 * any PHP host out of the box.
 */
class MiniPDF
{
    private array $ops = [];
    private float $width = 595.28;  // A4 pt
    private float $height = 841.89;

    public function setFont(string $style = 'F1', int $size = 11): void
    {
        $this->ops[] = "/$style $size Tf";
    }

    public function text(float $x, float $y, string $text, string $style = 'F1', int $size = 11): void
    {
        $safe = $this->escape($text);
        $this->ops[] = "BT /$style $size Tf 1 0 0 1 $x " . ($this->height - $y) . " Tm ($safe) Tj ET";
    }

    public function line(float $x1, float $y1, float $x2, float $y2, float $width = 0.6): void
    {
        $y1 = $this->height - $y1;
        $y2 = $this->height - $y2;
        $this->ops[] = "$width w $x1 $y1 m $x2 $y2 l S";
    }

    public function rect(float $x, float $y, float $w, float $h, string $rgb = '0.94 0.92 0.85'): void
    {
        $y = $this->height - $y - $h;
        $this->ops[] = "$rgb rg $x $y $w $h re f";
    }

    private function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    /** Builds the PDF and returns the raw bytes. */
    public function output(): string
    {
        $content = implode("\n", $this->ops);
        $objects = [];

        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $objects[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->width} {$this->height}] "
                    . "/Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> /Contents 4 0 R >>";
        $objects[4] = "<< /Length " . strlen($content) . " >>\nstream\n$content\nendstream";
        $objects[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $objects[6] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>";

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $num => $body) {
            $offsets[$num] = strlen($pdf);
            $pdf .= "$num 0 obj\n$body\nendobj\n";
        }
        $xrefStart = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 $count\n0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= str_pad((string)$offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }
        $pdf .= "trailer\n<< /Size $count /Root 1 0 R >>\nstartxref\n$xrefStart\n%%EOF";

        return $pdf;
    }

    public function download(string $filename): void
    {
        $data = $this->output();
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($data));
        echo $data;
        exit;
    }
}

/**
 * Builds the full booking invoice document for a given booking + room.
 */
function build_booking_invoice_pdf(array $booking, array $room): MiniPDF
{
    $settings = get_settings();
    $pdf = new MiniPDF();

    // Header band
    $pdf->rect(0, 0, 595.28, 90, '0.12 0.29 0.23');
    $pdf->text(50, 45, $settings['site_name'] ?? "Blacky's Eco Resort", 'F2', 20);
    $pdf->text(50, 66, 'Booking Invoice / Confirmation', 'F1', 11);

    $y = 130;
    $pdf->text(50, $y, 'Invoice For:', 'F2', 11); $y += 18;
    $pdf->text(50, $y, $booking['full_name'], 'F1', 11); $y += 16;
    $pdf->text(50, $y, $booking['email'], 'F1', 11); $y += 16;
    $pdf->text(50, $y, $booking['phone'], 'F1', 11); $y += 30;

    $pdf->line(50, $y, 545, $y); $y += 26;

    $rows = [
        ['Booking Reference', $booking['booking_ref']],
        ['Room / Villa', $room['name']],
        ['Check-in', $booking['check_in']],
        ['Check-out', $booking['check_out']],
        ['Nights', (string)$booking['nights']],
        ['Guests', (string)$booking['guests']],
        ['Status', ucfirst($booking['status'])],
    ];
    foreach ($rows as [$label, $value]) {
        $pdf->text(50, $y, $label, 'F2', 11);
        $pdf->text(260, $y, (string)$value, 'F1', 11);
        $y += 22;
    }

    $y += 10;
    $pdf->line(50, $y, 545, $y); $y += 30;

    $pdf->text(50, $y, 'Rate per Night', 'F1', 11);
    $pdf->text(450, $y, format_price($room['price_per_night']), 'F1', 11);
    $y += 24;
    $pdf->text(50, $y, 'Total Amount Due', 'F2', 13);
    $pdf->text(450, $y, format_price($booking['total_amount']), 'F2', 13);
    $y += 50;

    if (!empty($booking['special_request'])) {
        $pdf->text(50, $y, 'Special Requests:', 'F2', 11); $y += 18;
        foreach (explode("\n", wordwrap($booking['special_request'], 90)) as $line) {
            $pdf->text(50, $y, $line, 'F1', 10);
            $y += 15;
        }
    }

    $pdf->text(50, 780, 'Thank you for choosing ' . ($settings['site_name'] ?? "Blacky's Eco Resort") . '. ' . ($settings['address'] ?? ''), 'F1', 9);
    return $pdf;
}
