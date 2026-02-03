package com.example.homi.data.model

data class ChangePasswordRequest(
    val current_password: String,
    val new_password: String,
    val new_password_confirmation: String
)

data class UpsertResidentProfileRequest(
    // dari login response ada "name" & "full_name"
    val name: String? = null,
    val full_name: String? = null,
    val phone: String? = null
)
