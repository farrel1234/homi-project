package com.example.homi.ui.screens

import androidx.compose.foundation.Image
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.homi.R

@Composable
fun TampilanAwalScreen3(
    onNextClicked: () -> Unit = {}
) {
    // Font
    val laBelleAurore = FontFamily(Font(R.font.la_belle_aurore))
    val poppins = FontFamily(Font(R.font.poppins_semibold))

    Box(modifier = Modifier.fillMaxSize()) {
        Image(
            painter = painterResource(id = R.drawable.loading_screen),
            contentDescription = "Background",
            modifier = Modifier.fillMaxSize(),
            contentScale = ContentScale.Crop
        )

        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(24.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Bottom
        ) {
            Text(
                text = "Menghubungkan Warga, Membangun Kebersamaan",
                fontSize = 22.sp,
                fontWeight = FontWeight.Bold,
                fontFamily = poppins,
                color = Color.White,
                textAlign = TextAlign.Center,
                modifier = Modifier.fillMaxWidth()
            )

            Spacer(modifier = Modifier.height(90.dp))

            // Area klik kanan-bawah
            Box(
                modifier = Modifier
                    .fillMaxWidth()
                    .clickable { onNextClicked() }, // klik area kosong kanan-bawah
                contentAlignment = Alignment.BottomEnd
            ) {
                Text(
                    text = "Mulai",
                    fontSize = 14.sp,
                    fontWeight = FontWeight.Bold,
                    fontFamily = poppins,
                    color = Color.White,
                    modifier = Modifier
                        .padding(end = 8.dp, bottom = 8.dp)
                        .clickable { onNextClicked() } // teks juga bisa diklik
                )
            }
        }
    }
}

@Preview(showSystemUi = true)
@Composable
fun PreviewTampilanAwal3() {
    MaterialTheme {
        TampilanAwalScreen3()
    }
}
