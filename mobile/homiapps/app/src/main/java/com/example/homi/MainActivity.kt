package com.example.homi

import android.os.Build
import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.annotation.RequiresApi
import com.example.homi.data.local.TokenStore
import com.example.homi.navigation.AppNavHostAnimated

class MainActivity : ComponentActivity() {
    @RequiresApi(Build.VERSION_CODES.O)
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val tokenStore = TokenStore(this)

        setContent {
            AppNavHostAnimated(tokenStore = tokenStore)
        }
    }
}
