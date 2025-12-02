package com.example.homi.ui.screens

import androidx.compose.animation.core.Animatable
import androidx.compose.animation.core.tween
import androidx.compose.foundation.Image
import androidx.compose.foundation.layout.*
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.graphicsLayer
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R
import kotlinx.coroutines.delay
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.remember

@Composable
fun SplashScreen(
    onSplashFinished: () -> Unit = {}
) {
    val laBelleAurore = FontFamily(Font(R.font.la_belle_aurore))

    // 🔹 Animasi alpha dari 0f → 1f
    val alpha = remember { Animatable(0f) }

    // Jalankan animasi fade-in logo & teks
    LaunchedEffect(Unit) {
        alpha.animateTo(
            targetValue = 1f,
            animationSpec = tween(durationMillis = 800)
        )
        delay(2000) // tampil total 2 detik
        onSplashFinished()
    }

    Surface(
        modifier = Modifier.fillMaxSize(),
        color = MaterialTheme.colorScheme.background
    ) {
        Box(
            modifier = Modifier.fillMaxSize(),
            contentAlignment = Alignment.Center
        ) {
            Column(
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.Center
            ) {
                Image(
                    painter = painterResource(id = R.drawable.homi_logo),
                    contentDescription = "Logo Homi",
                    modifier = Modifier
                        .size(220.dp)
                        .graphicsLayer(alpha = alpha.value)
                )

                Text(
                    text = "Homi",
                    fontSize = 45.sp,
                    fontFamily = laBelleAurore,
                    fontWeight = FontWeight.Bold,
                    color = Color(0xFFF7C0A2),
                    modifier = Modifier
                        .offset(y = (-40).dp)
                        .graphicsLayer(alpha = alpha.value)
                )
            }
        }
    }
}

@Preview(showSystemUi = true)
@Composable
fun PreviewSplashScreen() {
    MaterialTheme {
        SplashScreen()
    }
}
