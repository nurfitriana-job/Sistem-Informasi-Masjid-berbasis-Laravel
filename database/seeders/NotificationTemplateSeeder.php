<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Template notifikasi yang tersedia
     * Array ini memudahkan untuk menambah atau mengubah template
     *
     * @var array
     */
    protected $templates = [
        // Template kegiatan baru
        [
            'name' => 'kegiatan_masjid_create',
            'subject' => 'Kegiatan Masjid Baru',
            'body' => <<<'EOT'
🌟 **Kegiatan Baru Telah Ditambahkan**

*Nama Kegiatan:* {name}
*Kategori Kegiatan:* {category_name}
**Status Kegiatan**: {status}
Detail Kegiatan:
{description}

🕰️ **Tanggal dan Waktu Acara**:
- Mulai: {start_date}
- Selesai: {end_date}

🌟 **Untuk Informasi Lainnya**, hubungi pengurus masjid atau kunjungi website kami.
Semoga Allah memudahkan segala urusan kita. Aamiin.
EOT,
            'is_active' => true,
        ],
        [
            'name' => 'pembayaran_transaksi_accepted',
            'subject' => '{subject}',
            'body' => <<<'EOT'
        Assalamualaikum {user},
        pembayaran sebesar {nominal} untuk {jenis_transaksi} telah kami terima.
        Terima kasih atas partisipasi dan dukungannya kepada Masjid Baiturrahman.
        Semoga Allah membalas amal kebaikan Bapak/Ibu dengan pahala yang berlipat ganda dan keberkahan dalam rezeki. Aamiin.
        EOT,
            'is_active' => true,
        ],
        [
            'name' => 'pembayaran_transaksi_gagal',
            'subject' => '{subject}',
            'body' => <<<'EOT'
            Assalamu’alaikum {user}, ini adalah pengingat bahwa pembayaran untuk {jenis_transaksi} sebesar {nominal} belum kami terima.
            Mohon silahkan di cek ulang.
            Jika ada kesalahan dalam pembayaran, silakan hubungi kami di {phone} atau {email} untuk bantuan lebih lanjut.
            Kami siap membantu Anda.
            Terima kasih atas perhatian dan kerjasamanya.
            Semoga Allah memudahkan segala urusan kita. Aamiin.
            EOT,
            'is_active' => true,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Iterasi semua template dan buat/perbarui di database
        foreach ($this->templates as $templateData) {
            $this->createOrUpdateTemplate($templateData);
        }
    }

    /**
     * Membuat atau memperbarui template notifikasi
     */
    protected function createOrUpdateTemplate(array $templateData): void
    {
        NotificationTemplate::updateOrCreate(
            ['name' => $templateData['name']],
            [
                'subject' => $templateData['subject'],
                'body' => $templateData['body'],
                'is_active' => $templateData['is_active'] ?? true,
            ]
        );
    }
}
