package com.example.homi.util

import android.content.Context
import android.net.Uri
import android.provider.OpenableColumns
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import java.io.File
import java.io.FileOutputStream

object FileUtils {

    /**
     * Bikin MultipartBody.Part untuk upload file dari Uri.
     * Default fieldName = "proof_image" (sesuai validasi Laravel kamu).
     */
    fun uriToMultipart(
        context: Context,
        uri: Uri,
        partName: String = "proof_image"
    ): MultipartBody.Part {
        val resolver = context.contentResolver
        val mime = resolver.getType(uri) ?: "image/*"

        val inputStream = resolver.openInputStream(uri) ?: throw IllegalStateException("Cannot open uri")
        val bytes = inputStream.use { it.readBytes() }

        val fileName = "proof_${System.currentTimeMillis()}.jpg"
        val body = bytes.toRequestBody(mime.toMediaType())
        return MultipartBody.Part.createFormData(partName, fileName, body)
    }}

