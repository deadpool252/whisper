package com.example.wishper

import android.content.Intent
import android.os.Bundle
import android.view.Menu
import android.view.MenuInflater
import android.view.MenuItem
import androidx.appcompat.app.AppCompatActivity

class SearchActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_search)
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