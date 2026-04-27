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
    val isBlockedByArrears: Boolean = false,
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
        if (_state.value.loading) return

        val reporter = payload["nama_warga"]
            ?: payload["nama_pemohon"]
            ?: payload["nama_pelapor"]
            ?: payload["namaPelapor"]
            ?: payload["nama_lengkap"]
            ?: payload["nama"]
            ?: payload["reporter_name"]
            ?: "Warga"

        val place = payload["alamat"]
            ?: payload["alamatPelapor"]
            ?: payload["alamat_pelapor"]
            ?: payload["lokasi_layanan"]
            ?: "Perumahan"

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
                val errorMsg = e.message ?: "Gagal submit"
                var isBlocked = false
                var finalMsg = errorMsg

                if (e is retrofit2.HttpException) {
                    val errorBody = e.response()?.errorBody()?.string()
                    if (errorBody != null) {
                        try {
                            val json = com.google.gson.JsonParser.parseString(errorBody).asJsonObject
                            if (json.has("status") && json.get("status").asString == "blocked_by_arrears") {
                                isBlocked = true
                                finalMsg = if (json.has("message")) json.get("message").asString else errorMsg
                            } else if (json.has("message")) {
                                finalMsg = json.get("message").asString
                            }
                        } catch (_: Exception) {}
                    }
                }

                _state.value = SuratSubmitState(
                    error = finalMsg,
                    isBlockedByArrears = isBlocked
                )
            }
        }
    }

    fun submitByTypeKeywords(
        fallbackRequestTypeId: Int,
        typeKeywords: List<String>,
        subject: String,
        payload: Map<String, String>
    ) {
        if (_state.value.loading) return

        val reporter = payload["nama_warga"]
            ?: payload["nama_pemohon"]
            ?: payload["nama_pelapor"]
            ?: payload["namaPelapor"]
            ?: payload["nama_lengkap"]
            ?: payload["nama"]
            ?: payload["reporter_name"]
            ?: "Warga"

        val place = payload["alamat"]
            ?: payload["alamatPelapor"]
            ?: payload["alamat_pelapor"]
            ?: payload["lokasi_layanan"]
            ?: "Perumahan"

        viewModelScope.launch {
            _state.value = SuratSubmitState(loading = true)

            try {
                val resolved = runCatching {
                    repo.resolveRequestTypeId(
                        keywords = typeKeywords,
                        letterOnly = true
                    )
                }.getOrNull()

                val newId = repo.submitSurat(
                    requestTypeId = resolved ?: fallbackRequestTypeId,
                    subject = subject,
                    reporterName = reporter,
                    requestDateIso = todayIso(),
                    place = place,
                    dataInput = payload
                )

                _state.value = SuratSubmitState(createdId = newId)
            } catch (e: Exception) {
                val errorMsg = e.message ?: "Gagal submit"
                var isBlocked = false
                var finalMsg = errorMsg

                if (e is retrofit2.HttpException) {
                    val errorBody = e.response()?.errorBody()?.string()
                    if (errorBody != null) {
                        try {
                            val json = com.google.gson.JsonParser.parseString(errorBody).asJsonObject
                            if (json.has("status") && json.get("status").asString == "blocked_by_arrears") {
                                isBlocked = true
                                finalMsg = if (json.has("message")) json.get("message").asString else errorMsg
                            } else if (json.has("message")) {
                                finalMsg = json.get("message").asString
                            }
                        } catch (_: Exception) {}
                    }
                }

                _state.value = SuratSubmitState(
                    error = finalMsg,
                    isBlockedByArrears = isBlocked
                )
            }
        }
    }

    fun reset() {
        _state.value = SuratSubmitState()
    }
}
