<?php

return [
    'label' => 'Workflow',
    'plural_label' => 'Workflows',
    'view_logs' => 'Lihat Log',
    'event_type' => 'Jenis Event',
    'condition_type' => 'Jenis Kondisi',

    'sections' => [
        'description' => [
            'label' => 'Deskripsi',
            'description' => 'Berikan deskripsi rinci tentang apa yang dilakukan alur kerja ini',
            'placeholder' => 'Masukkan deskripsi alur kerja...',
        ],
        'grouping' => [
            'label' => 'Grup',
            'all' => 'Semua',
        ],
        'type' => [
            'label' => [
                'workflow_type' => 'Tipe',
            ],
            'description' => 'Tentukan apa yang memicu alur kerja ini',
        ],
        'workflow_custom_event' => 'Konfigurasi Event Kustom',
        'actions' => [
            'label' => 'Aksi',
            'description' => 'Konfigurasikan aksi yang akan dijalankan ketika alur kerja ini dipicu',
        ],
        'status' => [
            'label' => 'Aktif',
            'description' => 'Aktifkan atau nonaktifkan alur kerja ini',
        ],
    ],

    'workflow' => [
        'types' => [
            'scheduled' => 'Terjadwal',
            'model_event' => 'Event Data',
            'custom_event' => 'Event Kustom',
        ],
    ],

    'schedule' => [
        'frequency' => [
            'label' => 'Frekuensi Jadwal',
            'options' => [
                'every_second' => 'Setiap Detik',
                'every_two_seconds' => 'Setiap 2 Detik',
                'every_five_seconds' => 'Setiap 5 Detik',
                'every_ten_seconds' => 'Setiap 10 Detik',
                'every_fifteen_seconds' => 'Setiap 15 Detik',
                'every_twenty_seconds' => 'Setiap 20 Detik',
                'every_thirty_seconds' => 'Setiap 30 Detik',
                'every_minute' => 'Setiap Menit',
                'every_two_minutes' => 'Setiap 2 Menit',
                'every_three_minutes' => 'Setiap 3 Menit',
                'every_four_minutes' => 'Setiap 4 Menit',
                'every_five_minutes' => 'Setiap 5 Menit',
                'every_ten_minutes' => 'Setiap 10 Menit',
                'every_fifteen_minutes' => 'Setiap 15 Menit',
                'every_thirty_minutes' => 'Setiap 30 Menit',
                'hourly' => 'Setiap Jam',
                'every_two_hours' => 'Setiap 2 Jam',
                'every_three_hours' => 'Setiap 3 Jam',
                'every_four_hours' => 'Setiap 4 Jam',
                'every_six_hours' => 'Setiap 6 Jam',
                'daily' => 'Harian',
                'daily_at' => 'Harian Pada',
                'twice_daily' => 'Dua Kali Sehari',
                'twice_daily_at' => 'Dua Kali Sehari Pada',
                'weekly' => 'Mingguan',
                'weekly_on' => 'Mingguan Pada',
                'monthly' => 'Bulanan',
                'monthly_on' => 'Bulanan Pada',
                'twice_monthly' => 'Dua Kali Sebulan',
                'last_day_of_month' => 'Hari Terakhir Bulan',
                'quarterly' => 'Triwulan',
                'quarterly_on' => 'Triwulan Pada',
                'yearly' => 'Tahunan',
                'yearly_on' => 'Tahunan Pada',
            ],
            'helper_text' => 'Masukkan waktu dalam format 24 jam (HH:mm)',
        ],
    ],

    'model' => [
        'events' => [
            'created' => 'Data Baru Dibuat',
            'updated' => 'Data Diperbarui',
            'deleted' => 'Data Dihapus',
        ],
        'attributes' => [
            'label' => 'Atribut Model',
            'any' => 'Semua Atribut',
            'updated' => 'Atribut Diperbarui',
        ],
    ],

    'conditions' => [
        'label' => 'Kondisi',
        'types' => [
            'none' => 'Tidak ada kondisi',
            'all' => 'Semua kondisi harus benar',
            'any' => 'Salah satu kondisi harus benar',
        ],
        'operators' => [
            'equals' => 'Sama dengan',
            'not_equals' => 'Tidak sama dengan',
            'greater_equals' => 'Lebih besar atau sama dengan',
            'less_equals' => 'Lebih kecil atau sama dengan',
            'greater' => 'Lebih besar',
            'less' => 'Lebih kecil',
        ],
    ],

    'custom_event' => [
        'label' => 'Event Kustom',
    ],

    'actions' => [
        'magic_attributes' => [
            'label' => 'Lihat atribut otomatis',
        ],
    ],

    'form' => [
        'run_once' => 'Jalankan Sekali',
        'active' => 'Aktif',
        'compare_value' => 'Bandingkan Nilai',
        'operator' => 'Operator',
    ],

    'table' => [
        'columns' => [
            'description' => 'Deskripsi',
            'type' => 'Tipe',
            'group' => 'Grup',
            'actions' => 'Aksi',
            'executions_count' => 'Jumlah Eksekusi',
            'last_execution' => 'Eksekusi Terakhir',
            'active' => 'Aktif',
        ],
        'types' => [
            'scheduled' => 'Terjadwal',
            'model_event' => 'Event Model',
            'custom_event' => 'Event Kustom',
            'default' => 'Default',
        ],
        'test' => [
            'title' => 'Uji Alur Kerja',
            'note' => 'Saat ini hanya mendukung event "created".',
            'fields' => [
                'description' => 'Deskripsi',
                'record_event' => 'Event Data',
                'record_id' => 'ID',
                'simulate_attributes' => [
                    'label' => 'Simulasikan Perubahan Atribut',
                    'help' => 'Ini tidak akan mengubah data',
                ],
                'execute_actions' => 'Jalankan Aksi',
            ],
            'notifications' => [
                'conditions_met' => 'Kondisi terpenuhi!',
                'execution_complete' => 'Eksekusi selesai, cek log',
                'conditions_not_met' => 'Kondisi tidak terpenuhi',
            ],
        ],
    ],
];
