package com.example.homi.data.repository

import com.example.homi.data.model.CreateServiceRequestBody
import com.example.homi.data.model.RequestTypeDto
import com.example.homi.data.model.ServiceRequestDto
import com.example.homi.data.remote.ApiService

class ServiceRequestRepository(
    private val api: ApiService
) {
    suspend fun getRequestTypes(): List<RequestTypeDto> =
        api.getRequestTypes().data

    suspend fun resolveRequestTypeId(
        keywords: List<String>,
        letterOnly: Boolean = true
    ): Int? {
        if (keywords.isEmpty()) return null

        val lowers = keywords.map { it.trim().lowercase() }.filter { it.isNotBlank() }
        if (lowers.isEmpty()) return null

        val all = getRequestTypes()
        val source = if (letterOnly) all.filter { it.letterTypeId != null } else all

        return source.firstOrNull { t ->
            val name = t.name.lowercase()
            lowers.any { kw -> name.contains(kw) }
        }?.id
    }

    private fun snakeToCamel(key: String): String {
        val k = key.trim()
        if (!k.contains('_')) return k

        val parts = k.split('_').filter { it.isNotBlank() }
        if (parts.isEmpty()) return k

        val head = parts.first()
        val tail = parts.drop(1).joinToString("") { p ->
            p.replaceFirstChar { ch -> ch.uppercase() }
        }
        return head + tail
    }

    private fun normalizeDataInput(input: Map<String, String>): Map<String, String> {
        // snake_case -> camelCase supaya cocok dengan template_html ({{namaUsaha}}, {{tujuanInstansi}}, dll)
        return input.entries.associate { (k, v) ->
            snakeToCamel(k) to v
        }
    }

    suspend fun submitSurat(
        requestTypeId: Int,
        subject: String,
        reporterName: String,
        requestDateIso: String,
        place: String,
        dataInput: Map<String, String>
    ): Long {
        val normalized = normalizeDataInput(dataInput)

        val resp = api.createServiceRequest(
            CreateServiceRequestBody(
                requestTypeId = requestTypeId,
                reporterName = reporterName,
                requestDate = requestDateIso,
                place = place,
                subject = subject,
                dataInput = normalized
            )
        )
        return resp.data.id
    }

    // ✅ list riwayat warga
    suspend fun listMy(): List<ServiceRequestDto> = api.getServiceRequests().data

    suspend fun detail(id: Long) = api.getServiceRequestDetail(id).data

    suspend fun downloadPdfBytes(id: Long): ByteArray {
        val resp = api.downloadServiceRequestPdf(id)
        if (!resp.isSuccessful) throw IllegalStateException("HTTP ${resp.code()}")
        return resp.body()?.bytes() ?: throw IllegalStateException("PDF kosong")
    }
}
