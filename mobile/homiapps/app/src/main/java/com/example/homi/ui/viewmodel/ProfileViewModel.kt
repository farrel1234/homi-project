package com.example.homi.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.homi.data.model.FullProfileResponse
import com.example.homi.data.repository.AccountRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class ProfileUiState(
    val loading: Boolean = false,
    val profile: FullProfileResponse? = null,
    val success: Boolean = false,
    val error: String? = null
)

class ProfileViewModel(private val repository: AccountRepository) : ViewModel() {

    private val _state = MutableStateFlow(ProfileUiState())
    val state: StateFlow<ProfileUiState> = _state.asStateFlow()

    fun loadProfile() {
        viewModelScope.launch {
            _state.value = _state.value.copy(loading = true, error = null)
            try {
                val data = repository.getFullProfile()
                _state.value = _state.value.copy(loading = false, profile = data)
            } catch (e: Exception) {
                _state.value = _state.value.copy(loading = false, error = e.message)
            }
        }
    }

    fun updateProfile(
        fullName: String,
        phone: String,
        nik: String,
        blok: String,
        noRumah: String,
        rt: String,
        rw: String,
        namaRt: String,
        pekerjaan: String,
        tempatLahir: String,
        tanggalLahir: String,
        jenisKelamin: String,
        houseType: String
    ) {
        viewModelScope.launch {
            _state.value = _state.value.copy(loading = true, error = null, success = false)
            try {
                repository.updateFullProfile(fullName, phone, nik, blok, noRumah, rt, rw, namaRt, pekerjaan, tempatLahir, tanggalLahir, jenisKelamin, houseType)
                _state.value = _state.value.copy(loading = false, success = true)
            } catch (e: Exception) {
                _state.value = _state.value.copy(loading = false, error = e.message)
            }
        }
    }

    fun resetState() {
        _state.value = _state.value.copy(success = false, error = null)
    }
}
