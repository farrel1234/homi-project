// File: app/src/main/java/com/example/homi/data/repository/FeeRepository.kt
package com.example.homi.data.repository

import com.example.homi.data.model.FeeInvoiceDto
import com.example.homi.data.model.PayInvoiceResponse
import com.example.homi.data.model.QrCodeDto
import com.example.homi.data.remote.ApiService
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody
import okhttp3.RequestBody.Companion.toRequestBody

class FeeRepository(private val api: ApiService) {

    suspend fun getInvoices(): List<FeeInvoiceDto> =
        api.getFeeInvoices().data

    // kalau ApiService kamu return ActiveQrResponse
    // FeeRepository.kt
    suspend fun getActiveQr(): QrCodeDto =
        api.getActiveFeeQr().data ?: throw Exception("QR aktif tidak ditemukan")



    suspend fun uploadProof(
        invoiceId: Long,
        proofPart: MultipartBody.Part,
        note: String? = null
    ): PayInvoiceResponse {
        val notePart: RequestBody? = note
            ?.takeIf { it.isNotBlank() }
            ?.toRequestBody("text/plain".toMediaType())

        // paling aman panggil tanpa named args biar gak mismatch nama param
        return api.payInvoice(invoiceId, proofPart, notePart)
    }
}
