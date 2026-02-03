// File: app/src/main/java/com/example/homi/ui/viewmodel/PembayaranIuranViewModel.kt
package com.example.homi.ui.viewmodel

import android.content.Context
import android.net.Uri
import android.util.Log
import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import androidx.lifecycle.viewModelScope
import com.example.homi.data.local.TokenStore
import com.example.homi.data.model.PayInvoiceResponse
import com.example.homi.data.model.QrCodeDto
import com.example.homi.data.remote.ApiClient
import com.example.homi.data.repository.FeeRepository
import com.example.homi.util.FileUtils
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import retrofit2.HttpException

data class PembayaranIuranState(
    val loading: Boolean = false,
    val uploading: Boolean = false,
    val qr: QrCodeDto? = null,
    val selectedProofUri: Uri? = null,
    val successMessage: String? = null,
    val error: String? = null
)

class PembayaranIuranViewModel(
    private val feeRepo: FeeRepository
) : ViewModel() {

    private val _state = MutableStateFlow(PembayaranIuranState())
    val state: StateFlow<PembayaranIuranState> = _state.asStateFlow()

    fun setProof(uri: Uri?) {
        _state.value = _state.value.copy(
            selectedProofUri = uri,
            successMessage = null,
            error = null
        )
    }

    fun loadQr() {
        viewModelScope.launch {
            _state.value = _state.value.copy(loading = true, error = null)
            try {
                val qr = feeRepo.getActiveQr()
                _state.value = _state.value.copy(loading = false, qr = qr)
            } catch (e: Exception) {
                _state.value = _state.value.copy(
                    loading = false,
                    error = e.message ?: "Gagal mengambil QR aktif"
                )
            }
        }
    }

    // File: app/src/main/java/com/example/homi/ui/viewmodel/PembayaranIuranViewModel.kt
    fun uploadProof(context: Context, invoiceId: Long) {
        val uri = _state.value.selectedProofUri
        if (uri == null) {
            _state.value = _state.value.copy(error = "Pilih foto bukti dulu")
            return
        }

        viewModelScope.launch {
            _state.value = _state.value.copy(uploading = true, successMessage = null, error = null)
            try {
                val part = FileUtils.uriToMultipart(context, uri) // proof_image
                val res: PayInvoiceResponse = feeRepo.uploadProof(invoiceId, part)

                _state.value = _state.value.copy(
                    uploading = false,
                    successMessage = res.message ?: "Berhasil upload bukti pembayaran"
                )
            } catch (e: HttpException) {
                val body = e.response()?.errorBody()?.string()
                _state.value = _state.value.copy(
                    uploading = false,
                    error = body ?: "Upload gagal (HTTP ${e.code()})"
                )
            } catch (e: Exception) {
                _state.value = _state.value.copy(
                    uploading = false,
                    error = e.message ?: "Upload gagal"
                )
            }
        }
    }


    class PembayaranIuranViewModelFactory(
    private val tokenStore: TokenStore
) : ViewModelProvider.Factory {
        @Suppress("UNCHECKED_CAST")
        override fun <T : ViewModel> create(modelClass: Class<T>): T {
            val api = ApiClient.getApi(tokenStore)
            val repo = FeeRepository(api)
            return PembayaranIuranViewModel(repo) as T
        }
    }}
