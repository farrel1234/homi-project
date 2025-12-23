package com.example.homi.data.repository

import com.example.homi.data.model.DirectoryItem
import com.example.homi.data.remote.ApiService

class DirectoryRepository(private val api: ApiService) {
    suspend fun getDirectory(q: String? = null): List<DirectoryItem> {
        return api.getDirectory(q).data
    }
}
