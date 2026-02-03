package com.example.homi.data.model

import com.google.gson.annotations.SerializedName

// ===== REQUEST TYPES =====
data class RequestTypeDto(
    val id: Int,
    val name: String,
    @SerializedName("letter_type_id") val letterTypeId: Int?
)

data class RequestTypesResponse(
    val data: List<RequestTypeDto>
)

// ===== SERVICE REQUESTS =====
data class CreateServiceRequestBody(
    @SerializedName("request_type_id") val requestTypeId: Int,
    @SerializedName("reporter_name") val reporterName: String,
    @SerializedName("request_date") val requestDate: String, // yyyy-MM-dd
    val place: String,
    val subject: String,
    @SerializedName("data_input") val dataInput: Map<String, String> = emptyMap()
)

data class ServiceRequestTypeDto(
    val id: Int,
    val name: String,
    @SerializedName("letter_type_id") val letterTypeId: Int? = null
)

data class ServiceRequestUserDto(
    val id: Long,
    val name: String?,
    val email: String?
)

data class ServiceRequestDto(
    val id: Long,
    @SerializedName("user_id") val userId: Long,
    @SerializedName("request_type_id") val requestTypeId: Int,
    @SerializedName("reporter_name") val reporterName: String?,
    @SerializedName("request_date") val requestDate: String?,
    val place: String?,
    val subject: String?,
    @SerializedName("data_input") val dataInput: Map<String, String>?,
    val status: String,
    @SerializedName("admin_note") val adminNote: String?,
    @SerializedName("pdf_path") val pdfPath: String?,
    @SerializedName("pdf_url") val pdfUrl: String?,
    val type: ServiceRequestTypeDto?,
    val user: ServiceRequestUserDto?
)

data class CreateServiceRequestResponse(
    val message: String?,
    val data: ServiceRequestDto
)

data class ServiceRequestsResponse(
    val data: List<ServiceRequestDto>
)

data class ServiceRequestDetailResponse(
    val data: ServiceRequestDto
)

// ===== konstanta mapping request_types.id di DB kamu =====
object RequestTypeIds {
    const val SURAT_PENGANTAR = 1
    const val SURAT_DOMISILI = 5
    const val SURAT_KEMATIAN = 6
    const val SURAT_USAHA = 7
    const val SURAT_BELUM_MENIKAH = 8
}
