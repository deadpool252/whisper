package com.example.wishper

import android.content.Intent
import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.view.Menu
import android.view.MenuInflater
import android.view.MenuItem
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import com.google.android.material.bottomnavigation.BottomNavigationView
import java.nio.file.Files.find

class MainActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        val userIdEdit = findViewById<EditText>(R.id.userIdEdit)
        val passwordEdit = findViewById<EditText>(R.id.passwordEdit)
        val loginButton = findViewById<Button>(R.id.loginButton)
        val createButton = findViewById<Button>(R.id.createButton)

        loginButton.setOnClickListener{
            val userid = userIdEdit.text.toString()
            val password = passwordEdit.text.toString()
            Toast.makeText(this,userid + password, Toast.LENGTH_LONG).show()
        }
        createButton.setOnClickListener{
            Toast.makeText(this,"create", Toast.LENGTH_LONG).show()
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
        //val intent3 = Intent(this, WishperActivity::class.java)
        //val intent4 = Intent(this, ProfileActivity::class.java)
        val intent5 = Intent(this, UserEditActivity::class.java)
        //val intent6 = Intent(this, LogoutActivity::class.java)

        when(item.itemId){
            //R.id.timeline -> startActivity(intent1)
            R.id.search -> startActivity(intent2)
            //R.id.wishper -> startActivity(intent3)
            //R.id.myprofile -> startActivity(intent4)
            R.id.profileedit -> startActivity(intent5)
            //R.id.logout -> startActivity(intent6)
        }
        return super.onOptionsItemSelected(item)
    }
}