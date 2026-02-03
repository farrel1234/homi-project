package com.example.homi.data.remote

import com.example.homi.data.model.ApiResponse
import com.google.gson.annotations.SerializedName
import okhttp3.ResponseBody
import retrofit2.Response
import retrofit2.http.*

data class RequestTypeDto(
    val id: Int,
    val name: String,
    @SerializedName("letter_type_id") val letterTypeId: Int?
)

data class ServiceRequestTypeDto(
    val id: Int,
    val name: String,
    @SerializedName("letter_type_id") val letterTypeId: Int? = null
)

data class ServiceRequestDto(
    val id: Long,
    @SerializedName("request_type_id") val requestTypeId: Int,
    @SerializedName("reporter_name") val reporterName: String?,
    @SerializedName("request_date") val requestDate: String?,
    val place: String?,
    val subject: String?,
    @SerializedName("data_input") val dataInput: Map<String, Any>?,
    val status: String,
    @SerializedName("admin_note") val adminNote: String?,
    @SerializedName("pdf_url") val pdfUrl: String?,
    val type: ServiceRequestTypeDto?
)

data class CreateServiceRequestBody(
    @SerializedName("request_type_id") val requestTypeId: Int,
    @SerializedName("reporter_name") val reporterName: String,
    @SerializedName("request_date") val requestDate: String, // yyyy-MM-dd
    val place: String,
    val subject: String,
    @SerializedName("data_input") val dataInput: Map<String, Any> = emptyMap()
)

interface HomiApi {

    @GET("request-types")
    suspend fun getRequestTypes(
        @Header("Authorization") bearer: String
    ): ApiResponse<List<RequestTypeDto>>

    @POST("service-requests")
    suspend fun createServiceRequest(
        @Header("Authorization") bearer: String,
        @Body body: CreateServiceRequestBody
    ): ApiResponse<ServiceRequestDto>

    @GET("service-requests/{id}")
    suspend fun getServiceRequestDetail(
        @Header("Authorization") bearer: String,
        @Path("id") id: Long
    ): ApiResponse<ServiceRequestDto>

    @Streaming
    @GET("service-requests/{id}/download")
    suspend fun downloadServiceRequestPdf(
        @Header("Authorization") bearer: String,
        @Path("id") id: Long
    ): Response<ResponseBody>
}
