package com.example.homi.data.model

data class OkResponse(
    val success: Boolean? = null,
    val status: Boolean? = null,
    val message: String? = null,
    val data: Any? = null,
    val errors: Any? = null
)
