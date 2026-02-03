package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import com.example.homi.data.repository.DirectoryRepository

class DirectoryViewModelFactory(
    private val repo: DirectoryRepository
) : ViewModelProvider.Factory {

    override fun <T : ViewModel> create(modelClass: Class<T>): T {
        if (modelClass.isAssignableFrom(DirectoryViewModel::class.java)) {
            @Suppress("UNCHECKED_CAST")
            return DirectoryViewModel(repo) as T
        }
        throw IllegalArgumentException("Unknown ViewModel class: ${modelClass.name}")
    }
}
