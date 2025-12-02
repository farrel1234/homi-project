package com.example.homi

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import com.example.homi.navigation.AppNavHostAnimated

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        setContent {
            // Kalau punya theme sendiri pakai di sini:
            // HomiTheme { AppNavHostAnimated() }
            AppNavHostAnimated()
        }
    }
}
