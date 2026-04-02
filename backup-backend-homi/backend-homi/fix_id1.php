<?php

use App\Models\LetterType;

echo "Fixing required_json for Letter ID 1...\n";
LetterType::where('id', 1)->update([
    'required_json' => ['nomor_surat', 'tanggal_surat', 'nama', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'rt', 'rw', 'nama_rt', 'keperluan']
]);

echo "Fixed!";
