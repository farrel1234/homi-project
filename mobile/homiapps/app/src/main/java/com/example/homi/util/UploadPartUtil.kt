package com.example.homi.util

import android.content.ContentResolver
import android.content.Context
import android.net.Uri
import android.provider.OpenableColumns
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import java.io.File
import java.io.FileOutputStream

object UploadPartUtil {

    fun uriToImagePart(
        context: Context,
        uri: Uri,
        fieldName: String = "proof_image" // <- WAJIB ini sesuai backend
    ): MultipartBody.Part {
        val cr = context.contentResolver
        val mime = cr.getType(uri) ?: "image/*"
        val fileName = queryDisplayName(cr, uri) ?: "proof_${System.currentTimeMillis()}.jpg"

        val tempFile = File(context.cacheDir, fileName)

        cr.openInputStream(uri)?.use { input ->
            FileOutputStream(tempFile).use { output ->
                input.copyTo(output)
            }
        } ?: throw IllegalArgumentException("Gagal buka file dari uri: $uri")

        val body = tempFile.asRequestBody(mime.toMediaTypeOrNull())
        return MultipartBody.Part.createFormData(fieldName, tempFile.name, body)
    }

    private fun queryDisplayName(cr: ContentResolver, uri: Uri): String? {
        val cursor = cr.query(uri, null, null, null, null) ?: return null
        cursor.use {
            val idx = it.getColumnIndex(OpenableColumns.DISPLAY_NAME)
            if (idx == -1) return null
            it.moveToFirst()
            return it.getString(idx)
        }
    }
}
