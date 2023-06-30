package com.example.wishper

import android.view.View
import android.widget.Button
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView

class WhisperHolder(itemView: View): RecyclerView.ViewHolder(itemView) {
    val userImage : ImageView = itemView.findViewById(R.id.userImage)
    val userNameText : TextView = itemView.findViewById(R.id.userNameText)
    val whisperText : TextView = itemView.findViewById(R.id.whisperText)
    val goodImage : ImageView = itemView.findViewById(R.id.goodImage)
}