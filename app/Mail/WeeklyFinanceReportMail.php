<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Finance;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyFinanceReportMail extends Mailable
{
    use Queueable, SerializesModels;

    protected User $user;
    protected float $weeklyTotal;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, float $weeklyTotal)
    {
        $this->user = $user;
        $this->weeklyTotal = $weeklyTotal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laporan Keuangan Mingguan untuk ' . $this->user->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $transactions = Finance::where('users_id', $this->user->id)
            ->whereBetween('purchase_date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->get();

        $startDate = $transactions->min('purchase_date') ? Carbon::parse($transactions->min('purchase_date')) : Carbon::now()->subWeek()->startOfWeek();
        $endDate = $transactions->max('purchase_date') ? Carbon::parse($transactions->max('purchase_date')) : Carbon::now()->subWeek()->endOfWeek();

        return new Content(
            markdown: 'emails.expense-notification-weekly',
            with: [
                'user' => $this->user,
                'weeklyTotal' => $this->weeklyTotal,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'url' => config('app.url'),
            ],
        );
    }

    /**
     * Attach the PDF report to the email.
     */
    /**
     * Attach the PDF report to the email.
     */
    public function build()
    {
        $transactions = Finance::where('users_id', $this->user->id)
            ->whereBetween('purchase_date', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->get()->sortBy('purchase_date');

        $startDate = $transactions->min('purchase_date') ? Carbon::parse($transactions->min('purchase_date')) : Carbon::now()->subWeek()->startOfWeek();
        $endDate = $transactions->max('purchase_date') ? Carbon::parse($transactions->max('purchase_date')) : Carbon::now()->subWeek()->endOfWeek();

        // picture logo base64
        $path = base_path('public/logo.svg'); // local
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $pic  = 'data:image/' . $type . ';base64,' . base64_encode($data);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.weekly-report', [
            'user' => $this->user,
            'transactions' => $transactions,
            'weeklyTotal' => $this->weeklyTotal,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'logo' => $pic,
        ]);

        // Attach PDF
        return $this
            ->subject('Laporan Keuangan Mingguan untuk ' . $this->user->name)
            ->markdown('emails.expense-notification-weekly', [
                'user' => $this->user,
                'weeklyTotal' => $this->weeklyTotal,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'url' => config('app.url'),
            ])
            ->attachData($pdf->output(), 'Laporan-Keuangan-Mingguan-' . $this->user->name . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }

}
