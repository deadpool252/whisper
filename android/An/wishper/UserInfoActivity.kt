package com.example.wishper

import android.content.Intent
import android.media.Image
import android.os.Bundle
import android.provider.MediaStore.Audio.Radio
import android.view.Menu
import android.view.MenuInflater
import android.view.MenuItem
import android.widget.Button
import android.widget.ImageView
import android.widget.RadioButton
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.RecyclerView

class UserInfoActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_user_info)

        val userImage = findViewById<ImageView>(R.id.userImage)
        val followButton = findViewById<Button>(R.id.followButton)
        val whisperRadio = findViewById<RadioButton>(R.id.wishperRadio)
        val goodInfoRadio = findViewById<RadioButton>(R.id.goodInfoRadio)
        val userRecycle = findViewById<RecyclerView>(R.id.userRecycle)

        followButton.setOnClickListener{

        }

        whisperRadio.setOnClickListener{

        }

        goodInfoRadio.setOnClickListener{

        }

    }

    override fun onCreateOptionsMenu(menu: Menu?): Boolean {
        val inflater: MenuInflater
        menuInflater.inflate(R.menu.overflow_menu, menu)
        return true
    }

    override fun onOptionsItemSelected(item: MenuItem): Boolean {

        //val intent1 = Intent(this, TimeLineActivity::class.java)
        val intent2 = Intent(this, SearchActivity::class.java)
        //val intent3 = Intent(this, WhisperActivity::class.java)
        val intent4 = Intent(this, UserInfoActivity::class.java)
        val intent5 = Intent(this, UserEditActivity::class.java)
        //val intent6 = Intent(this, LogoutActivity::class.java)

        when(item.itemId){
            //R.id.timeline -> startActivity(intent1)
            R.id.search -> startActivity(intent2)
            //R.id.wishper -> startActivity(intent3)
            R.id.myprofile -> startActivity(intent4)
            R.id.profileedit -> startActivity(intent5)
            //R.id.logout -> startActivity(intent6)
        }
        return super.onOptionsItemSelected(item)
    }
}