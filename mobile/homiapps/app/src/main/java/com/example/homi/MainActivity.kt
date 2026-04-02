package com.example.homi

import android.Manifest
import android.content.pm.PackageManager
import android.os.Build
import android.os.Bundle
import android.util.Log
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.activity.result.contract.ActivityResultContracts
import androidx.core.content.ContextCompat
import androidx.lifecycle.lifecycleScope
import com.example.homi.data.local.TokenStore
import com.example.homi.util.TokenManager
import com.example.homi.data.remote.ApiClient
import com.example.homi.navigation.AppNavHostAnimated
import com.google.firebase.messaging.FirebaseMessaging
import kotlinx.coroutines.launch

class MainActivity : ComponentActivity() {

    private val requestPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { isGranted: Boolean ->
        if (isGranted) {
            fetchAndSendFcmToken()
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        enableEdgeToEdge()
        super.onCreate(savedInstanceState)

        askNotificationPermission()
        fetchAndSendFcmToken()

        val tokenStore = TokenStore(this)

        setContent {
            AppNavHostAnimated(tokenStore = tokenStore)
        }
    }

    private fun askNotificationPermission() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.POST_NOTIFICATIONS) ==
                PackageManager.PERMISSION_GRANTED
            ) {
                // Granted
            } else {
                requestPermissionLauncher.launch(Manifest.permission.POST_NOTIFICATIONS)
            }
        }
    }

    private fun fetchAndSendFcmToken() {
        FirebaseMessaging.getInstance().token.addOnCompleteListener { task ->
            if (!task.isSuccessful) {
                Log.w("FCM", "Fetching FCM registration token failed", task.exception)
                return@addOnCompleteListener
            }
            val token = task.result
            Log.d("FCM_TOKEN", "Token: $token")
            
            val tokenManager = TokenManager(this@MainActivity)
            if (tokenManager.getAccessToken() != null) {
                lifecycleScope.launch {
                    try {
                        val api = ApiClient.getApi(TokenStore(this@MainActivity))
                        api.updateFcmToken(mapOf("fcm_token" to token))
                        Log.d("FCM_SYNC", "Token berhasil disinkronkan ke backend.")
                    } catch (e: Exception) {
                        Log.e("FCM_SYNC", "Gagal sinkron token", e)
                    }
                }
            }
        }
    }
}
