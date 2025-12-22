package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import com.example.homi.data.local.TokenStore
import com.example.homi.data.remote.ApiClient
import com.example.homi.data.repository.AnnouncementRepository

class AnnouncementViewModelFactory(
    private val tokenStore: TokenStore
) : ViewModelProvider.Factory {

    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        if (modelClass.isAssignableFrom(AnnouncementViewModel::class.java)) {
            val api = ApiClient.getApi(tokenStore)
            val repo = AnnouncementRepository(api)
            @Suppress("UNCHECKED_CAST")
            return AnnouncementViewModel(repo) as T
        }
        throw IllegalArgumentException("Unknown ViewModel class")
    }
}
