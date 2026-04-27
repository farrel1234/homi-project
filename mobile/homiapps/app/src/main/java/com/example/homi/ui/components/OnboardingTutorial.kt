package com.example.homi.ui.components

import androidx.compose.animation.*
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.window.Dialog
import androidx.compose.ui.window.DialogProperties
import com.example.homi.R

private val BlueMain = Color(0xFF2F7FA3)
private val AccentOrange = Color(0xFFF7A477)
private val PoppinsSemi = FontFamily(Font(R.font.poppins_semibold))
private val PoppinsReg = FontFamily(Font(R.font.poppins_regular))

data class OnboardingStep(
    val title: String,
    val description: String,
    val imageResId: Int? = null,
    val imageUrl: String? = null
)

@OptIn(ExperimentalAnimationApi::class)
@Composable
fun OnboardingTutorial(
    onDismiss: () -> Unit,
    onComplete: () -> Unit
) {
    var currentStep by remember { mutableStateOf(0) }
    
    val steps = remember {
        listOf(
            OnboardingStep(
                title = "Selamat Datang di Homi!",
                description = "Platform hunian pintar untuk kenyamanan dan kemudahan hidup Anda di perumahan.",
                imageResId = R.drawable.img_onboarding_1 // Fallback/Expected name
            ),
            OnboardingStep(
                title = "Pengumuman Harian",
                description = "Jangan lewatkan berita terbaru, jadwal kegiatan, dan informasi penting dari pengelola perumahan.",
                imageResId = R.drawable.img_onboarding_2
            ),
            OnboardingStep(
                title = "Urus Surat Tanpa Antre",
                description = "Ajukan surat domisili, pengantar, dan lainnya langsung dari genggaman Anda. Praktis dan cepat!",
                imageResId = R.drawable.img_onboarding_3
            ),
            OnboardingStep(
                title = "Bayar Iuran & Lapor Masalah",
                description = "Pantau tagihan bulanan dan laporkan masalah lingkungan hanya dalam hitungan detik.",
                imageResId = R.drawable.img_onboarding_4
            ),
            OnboardingStep(
                title = "Pantau Riwayat Layanan",
                description = "Semua riwayat pengajuan dan pembayaran Anda tersimpan rapi dan dapat diakses kapan saja.",
                imageResId = R.drawable.img_onboarding_5
            )
        )
    }

    Dialog(
        onDismissRequest = { /* Prevent dismissal by tapping outside */ },
        properties = DialogProperties(usePlatformDefaultWidth = false)
    ) {
        Surface(
            modifier = Modifier.fillMaxSize(),
            color = Color.White
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(24.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
                verticalArrangement = Arrangement.Center
            ) {
                // Skip Button (Top Right)
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.End
                ) {
                    TextButton(onClick = onDismiss) {
                        Text(
                            "Lewati",
                            fontFamily = PoppinsSemi,
                            color = Color.Gray,
                            fontSize = 14.sp
                        )
                    }
                }

                Spacer(Modifier.weight(0.5f))

                // Content Area with Animation
                AnimatedContent(
                    targetState = currentStep,
                    transitionSpec = {
                        fadeIn() with fadeOut()
                    },
                    modifier = Modifier.weight(3f)
                ) { stepIdx ->
                    val step = steps[stepIdx]
                    Column(
                        horizontalAlignment = Alignment.CenterHorizontally,
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        // Image Illustration
                        Box(
                            modifier = Modifier
                                .size(280.dp)
                                .clip(RoundedCornerShape(24.dp))
                                .background(BlueMain.copy(alpha = 0.05f)),
                            contentAlignment = Alignment.Center
                        ) {
                            if (step.imageResId != null) {
                                Image(
                                    painter = painterResource(step.imageResId),
                                    contentDescription = null,
                                    modifier = Modifier.fillMaxSize(),
                                    contentScale = ContentScale.Fit
                                )
                            }
                        }

                        Spacer(Modifier.height(40.dp))

                        Text(
                            text = step.title,
                            fontFamily = PoppinsSemi,
                            fontSize = 24.sp,
                            color = BlueMain,
                            textAlign = TextAlign.Center,
                            lineHeight = 32.sp
                        )

                        Spacer(Modifier.height(16.dp))

                        Text(
                            text = step.description,
                            fontFamily = PoppinsReg,
                            fontSize = 15.sp,
                            color = Color.Gray,
                            textAlign = TextAlign.Center,
                            lineHeight = 22.sp
                        )
                    }
                }

                // Step Indicators
                Row(
                    horizontalArrangement = Arrangement.spacedBy(8.dp),
                    modifier = Modifier.padding(vertical = 32.dp)
                ) {
                    steps.forEachIndexed { index, _ ->
                        Box(
                            modifier = Modifier
                                .size(if (index == currentStep) 24.dp else 8.dp, 8.dp)
                                .clip(RoundedCornerShape(4.dp))
                                .background(if (index == currentStep) AccentOrange else Color.LightGray)
                        )
                    }
                }

                // Next/Finish Button
                Button(
                    onClick = {
                        if (currentStep < steps.size - 1) {
                            currentStep++
                        } else {
                            onComplete()
                        }
                    },
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(56.dp),
                    shape = RoundedCornerShape(16.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = BlueMain)
                ) {
                    Text(
                        text = if (currentStep == steps.size - 1) "Mulai Sekarang" else "Lanjut",
                        fontFamily = PoppinsSemi,
                        fontSize = 16.sp,
                        color = Color.White
                    )
                }
                
                Spacer(Modifier.weight(0.5f))
            }
        }
    }
}
