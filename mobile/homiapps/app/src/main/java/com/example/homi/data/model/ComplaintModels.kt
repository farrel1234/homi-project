package com.example.homi.data.model

import com.google.gson.annotations.SerializedName

data class ComplaintDto(
    @SerializedName("id") val id: Long = 0L,

    // ===== request/create (yang selama ini kamu pakai) =====
    @SerializedName("nama_pelapor") val namaPelapor: String = "",
    @SerializedName("tanggal_pengaduan") val tanggalPengaduan: String = "", // legacy/create (yyyy-MM-dd)
    @SerializedName("tempat_kejadian") val tempatKejadian: String = "",      // legacy/create
    @SerializedName("perihal") val perihal: String = "",

    // ===== response terbaru dari backend kamu =====
    @SerializedName("tanggal") val tanggalLabel: String? = null,        // "3 Januari 2026"
    @SerializedName("tanggal_iso") val tanggalIso: String? = null,      // "2026-01-03T00:00:00.000000Z"
    @SerializedName("tempat") val tempat: String? = null,               // "bengkong botania"
    @SerializedName("foto_url") val fotoUrl: String? = null,            // full url

    // ===== optional lain (biar aman) =====
    @SerializedName("title") val title: String? = null,
    @SerializedName("description") val description: String? = null,
    @SerializedName("category") val category: String? = null,

    @SerializedName("foto_path") val fotoPath: String? = null,

    @SerializedName("status") val status: String = "baru",
    @SerializedName("status_en") val statusEn: String? = null,

    @SerializedName("created_at") val createdAt: String? = null,
    @SerializedName("updated_at") val updatedAt: String? = null,
    @SerializedName("resolved_at") val resolvedAt: String? = null,

    @SerializedName("user_id") val userId: Long? = null,
    @SerializedName("assigned_to") val assignedTo: Long? = null,
)

// ====== Response wrappers (biar aman kalau backend pakai "data") ======
data class ComplaintsResponse(
    @SerializedName("data") val data: List<ComplaintDto>? = null,
    @SerializedName("complaints") val complaints: List<ComplaintDto>? = null,
    @SerializedName("items") val items: List<ComplaintDto>? = null,
)

data class ComplaintDetailResponse(
    @SerializedName("data") val data: ComplaintDto? = null,
    @SerializedName("complaint") val complaint: ComplaintDto? = null,
)

data class CreateComplaintResponse(
    @SerializedName("message") val message: String? = null,
    @SerializedName("data") val data: ComplaintDto? = null,
    @SerializedName("complaint") val complaint: ComplaintDto? = null,
    @SerializedName("id") val id: Long? = null,
)
