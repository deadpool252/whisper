package com.example.wishper

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView

class WhisperAdapter() : RecyclerView.Adapter<WhisperHolder>() {
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): WhisperHolder {
        val itemXml = LayoutInflater.from(parent.context)
            .inflate(R.layout.whisper_row_layout,parent,false)
        return WhisperHolder(itemXml)
    }

    override fun onBindViewHolder(holder: WhisperHolder, position: Int) {
        TODO("Not yet implemented")
    }

    override fun getItemCount(): Int {
        TODO("Not yet implemented")
    }
}