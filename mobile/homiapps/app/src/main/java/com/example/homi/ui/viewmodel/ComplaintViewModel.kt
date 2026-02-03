package com.example.homi.ui.viewmodel

import android.content.Context
import android.net.Uri
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.homi.data.repository.ComplaintRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class ComplaintUiState(
    val loading: Boolean = false,
    val error: String? = null
)

class ComplaintViewModel(
    private val repo: ComplaintRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(ComplaintUiState())
    val uiState: StateFlow<ComplaintUiState> = _uiState.asStateFlow()

    /**
     * tanggalInputDdmmyyyy WAJIB: "ddMMyyyy" (8 digit), contoh: 09012026
     * fotoUri opsional (boleh null)
     */
    fun submitComplaint(
        context: Context,
        namaPelapor: String,
        tanggalInputDdmmyyyy: String,
        tempatKejadian: String,
        perihal: String,
        fotoUri: Uri?,
        onSuccess: (createdId: Long) -> Unit
    ) {
        _uiState.value = ComplaintUiState(loading = true, error = null)

        viewModelScope.launch {
            try {
                val created = repo.create(
                    context = context,
                    namaPelapor = namaPelapor.trim(),
                    tanggalInputDdmmyyyy = tanggalInputDdmmyyyy.trim(),
                    tempatKejadian = tempatKejadian.trim(),
                    perihal = perihal.trim(),
                    fotoUri = fotoUri // ✅ opsional
                )

                // repo.create kamu biasanya balikin DTO (punya id)
                val id = created?.id ?: 0L
                if (id == 0L) throw Exception("Response tidak valid (id kosong)")

                _uiState.value = ComplaintUiState(loading = false, error = null)
                onSuccess(id)
            } catch (e: Exception) {
                _uiState.value = ComplaintUiState(
                    loading = false,
                    error = e.message ?: "Gagal mengirim pengaduan"
                )
            }
        }
    }
}
