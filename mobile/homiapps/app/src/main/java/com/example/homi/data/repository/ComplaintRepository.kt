package com.example.homi.data.repository

import android.content.Context
import android.util.Log
import com.example.homi.data.model.ComplaintDto
import com.example.homi.data.remote.ApiService
import com.example.homi.util.FileUtils
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.RequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import retrofit2.HttpException
import java.time.LocalDate
import java.time.format.DateTimeFormatter

class ComplaintRepository(private val api: ApiService) {

    suspend fun list(): List<ComplaintDto> {
        val res = api.getComplaints()
        return res.data ?: res.complaints ?: res.items ?: emptyList()
    }

    suspend fun detail(id: Long): ComplaintDto {
        val res = api.getComplaintDetail(id)
        return res.data ?: res.complaint ?: error("Detail pengaduan tidak ditemukan")
    }

    /**
     * tanggalInputDdmmyyyy: user isi 8 digit ddmmyyyy
     * dikonversi ke yyyy-MM-dd sebelum dikirim (biar cocok kolom DB: date)
     */
    suspend fun create(
        context: Context,
        namaPelapor: String,
        tanggalInputDdmmyyyy: String,
        tempatKejadian: String,
        perihal: String,
        fotoUri: android.net.Uri? = null, // opsional
    ): ComplaintDto {
        val textPlain = "text/plain".toMediaType()

        val tanggalIso = ddMMyyyyToIso(tanggalInputDdmmyyyy)
            ?: throw IllegalArgumentException("Format tanggal harus ddmmyyyy (contoh: 09012026)")

        val namaPart: RequestBody = namaPelapor.trim().toRequestBody(textPlain)
        val tglPart: RequestBody = tanggalIso.toRequestBody(textPlain)
        val tempatPart: RequestBody = tempatKejadian.trim().toRequestBody(textPlain)
        val perihalPart: RequestBody = perihal.trim().toRequestBody(textPlain)

        val fotoPart = if (fotoUri != null) {
            // name part HARUS "foto" (backend biasanya request->file('foto'))
            FileUtils.uriToMultipart(context, fotoUri, "foto")
        } else null

        try {
            val res = api.createComplaint(
                namaPelapor = namaPart,
                tanggalPengaduan = tglPart,
                tempatKejadian = tempatPart,
                perihal = perihalPart,
                foto = fotoPart
            )

            // paling umum: res.data / res.complaint
            res.data?.let { return it }
            res.complaint?.let { return it }

            // fallback kalau backend cuma balikin id
            val id = res.id
            if (id != null) return detail(id)

            throw IllegalStateException(res.message ?: "Gagal membuat pengaduan")
        } catch (e: HttpException) {
            val err = e.response()?.errorBody()?.string()
            Log.e("COMPLAINT", "HTTP ${e.code()} err=$err")
            throw RuntimeException("HTTP ${e.code()} - ${err ?: "Validasi gagal"}")
        }
    }

    private fun ddMMyyyyToIso(input: String): String? {
        if (input.length != 8) return null
        if (!input.all { it.isDigit() }) return null

        // Android kamu sudah pakai O (lihat NavHost @RequiresApi O)
        val dt = LocalDate.parse(input, DateTimeFormatter.ofPattern("ddMMyyyy"))
        return dt.format(DateTimeFormatter.ISO_DATE) // yyyy-MM-dd
    }
}
