package com.example.homi.data.model

data class UpdateNameRequest(
    val name: String,
    val nama: String // biar aman kalau backend pakai "nama"
)
