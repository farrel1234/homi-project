// File: app/src/main/java/com/example/homi/data/model/FeeModels.kt
package com.example.homi.data.model

import com.google.gson.annotations.SerializedName

data class ActiveQrResponse(
    val data: QrCodeDto? = null
)

data class QrCodeDto(
    val id: Long = 0L,
    @SerializedName("is_active") val isActive: Boolean = false,
    val notes: String? = null,
    @SerializedName("image_url") val imageUrl: String? = null
)

data class FeeInvoicesResponse(
    val status: Boolean? = null,
    val data: List<FeeInvoiceDto> = emptyList()
)

data class FeeInvoiceDto(
    @SerializedName(value = "id", alternate = ["invoiceId"])
    val id: Long = 0L,

    @SerializedName(value = "fee_type", alternate = ["feeType"])
    val feeType: String? = null,

    @SerializedName(value = "amount", alternate = ["nominal"])
    val amount: Long = 0L,

    @SerializedName("status")
    val status: String = "",

    // ✅ dibuat nullable: lebih aman dari Gson yang bisa nyuntik null
    @SerializedName(value = "trx_id", alternate = ["trxId"])
    val trxId: String? = null,

    @SerializedName("period")
    val period: String = "",

    @SerializedName("due_date")
    val dueDate: String? = null
)

data class PayInvoiceResponse(
    val message: String? = null,
    val data: PayInvoiceData? = null
)

data class PayInvoiceData(
    @SerializedName("payment_id") val paymentId: Long = 0L,
    @SerializedName("proof_url") val proofUrl: String? = null
)
