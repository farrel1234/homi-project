package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import com.example.homi.data.repository.ServiceRequestRepository

class SuratSubmitViewModelFactory(
    private val repo: ServiceRequestRepository
) : ViewModelProvider.Factory {
    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        @Suppress("UNCHECKED_CAST")
        return SuratSubmitViewModel(repo) as T
    }
}
