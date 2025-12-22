package com.example.homi

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import com.example.homi.data.local.TokenStore
import com.example.homi.navigation.AppNavHostAnimated

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val tokenStore = TokenStore(this)

        setContent {
            AppNavHostAnimated(tokenStore = tokenStore)
        }
    }
}
