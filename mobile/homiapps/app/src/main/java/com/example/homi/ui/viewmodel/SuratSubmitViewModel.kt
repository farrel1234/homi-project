package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.homi.data.repository.ServiceRequestRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

data class SuratSubmitState(
    val loading: Boolean = false,
    val error: String? = null,
    val createdId: Long? = null
)

class SuratSubmitViewModel(
    private val repo: ServiceRequestRepository
) : ViewModel() {

    private val _state = MutableStateFlow(SuratSubmitState())
    val state: StateFlow<SuratSubmitState> = _state

    private fun todayIso(): String =
        SimpleDateFormat("yyyy-MM-dd", Locale.US).format(Date())

    fun submit(
        requestTypeId: Int,
        subject: String,
        payload: Map<String, String>
    ) {
        val reporter = payload["nama"]
            ?: payload["namaPelapor"]
            ?: payload["nama_pelapor"]
            ?: payload["nama_lengkap"]
            ?: payload["reporter_name"]
            ?: "Warga"

        val place = payload["alamat"]
            ?: payload["alamatPelapor"]
            ?: payload["alamat_pelapor"]
            ?: "Hawai Garden"

        viewModelScope.launch {
            _state.value = SuratSubmitState(loading = true)

            try {
                val newId = repo.submitSurat(
                    requestTypeId = requestTypeId,
                    subject = subject,
                    reporterName = reporter,
                    requestDateIso = todayIso(),
                    place = place,
                    dataInput = payload
                )

                _state.value = SuratSubmitState(createdId = newId)
            } catch (e: Exception) {
                _state.value = SuratSubmitState(
                    error = e.message ?: "Gagal submit"
                )
            }
        }
    }

    fun reset() {
        _state.value = SuratSubmitState()
    }
}
