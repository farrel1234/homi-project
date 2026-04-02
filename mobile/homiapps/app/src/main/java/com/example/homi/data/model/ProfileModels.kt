package com.example.homi.data.model

import com.google.gson.annotations.SerializedName

data class ResidentProfileDto(
    val id: Long? = null,
    @SerializedName("user_id") val userId: Long? = null,
    val nik: String? = null,
    val blok: String? = null,
    @SerializedName("no_rumah") val noRumah: String? = null,
    val rt: String? = null,
    val rw: String? = null,
    @SerializedName("nama_rt") val namaRt: String? = null,
    val alamat: String? = null,
    val pekerjaan: String? = null,
    @SerializedName("tempat_lahir") val tempatLahir: String? = null,
    @SerializedName("tanggal_lahir") val tanggalLahir: String? = null,
    @SerializedName("jenis_kelamin") val jenisKelamin: String? = null,
    @SerializedName("house_type") val houseType: String? = null,
    @SerializedName("is_public") val isPublic: Boolean? = null
)

data class FullProfileResponse(
    val id: Long,
    val name: String? = null,
    @SerializedName("full_name") val fullName: String? = null,
    val email: String? = null,
    val phone: String? = null,
    @SerializedName("resident_profile") val residentProfile: ResidentProfileDto? = null
)

data class UpdateProfileRequest(
    val name: String? = null,
    @SerializedName("full_name") val fullName: String? = null,
    val phone: String? = null
)

data class UpdateResidentProfileRequest(
    val nik: String? = null,
    val blok: String? = null,
    @SerializedName("no_rumah") val noRumah: String? = null,
    val rt: String? = null,
    val rw: String? = null,
    @SerializedName("nama_rt") val namaRt: String? = null,
    val alamat: String? = null,
    val pekerjaan: String? = null,
    @SerializedName("tempat_lahir") val tempatLahir: String? = null,
    @SerializedName("tanggal_lahir") val tanggalLahir: String? = null,
    @SerializedName("jenis_kelamin") val jenisKelamin: String? = null,
    @SerializedName("house_type") val houseType: String? = null
)
