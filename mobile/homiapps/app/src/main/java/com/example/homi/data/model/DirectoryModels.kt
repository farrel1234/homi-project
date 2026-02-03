package com.example.homi.data.model

import com.google.gson.annotations.SerializedName

data class DirectoryResponse(
    @SerializedName("current_page") val currentPage: Int? = null,
    @SerializedName("data") val data: List<DirectoryItem> = emptyList()
)

data class DirectoryItem(
    val id: Long,
    val name: String,
    val blok: String? = null,
    @SerializedName("no_rumah") val noRumah: String? = null,
    @SerializedName("blok_alamat") val blokAlamat: String? = null
)
