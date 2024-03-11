const express = require('express');
const router = express.Router();
const mysql = require('mysql');

const db =mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'laboratory_schedule_system'
})

router.get('/', (req, res)=>{
    const sql= "SELECT * FROM lecture_details";
    db.query(sql, (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})

router.post('/', (req,res)=>{
    const sql = "INSERT INTO lecture_details (lecture_id, full_name, user_name, faculty, email, mobil_number, password) VALUE (?)"
    const values=[
        req.body.id,
        req.body.name,
        req.body.userName,
        req.body.faculty,
        req.body.email,
        req.body.mobileNo,
        req.body.password
    ]
    db.query(sql, [values], (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})

router.delete('/:id', (req, res)=>{
    const sql ="DELETE FROM lecture_details WHERE lecture_id = ?";
    const id = req.params.id;

    db.query(sql,[id], (err, data)=>{
        if(err) return res.json(err);
        return res.json(data);
    })
})

router.put('/:updateId', (req, res)=>{
    const sql ="UPDATE lecture_details SET full_name=?, user_name=? , faculty=? , email=? , mobil_number=? , password=? WHERE lecture_id = ?";

    const values = [
        req.body.newName,
        req.body.newUserName,
        req.body.newFaculty,
        req.body.newEmail,
        req.body.newMobileNo,
        req.body.newPassword
    ];
    const id = req.params.updateId;

    db.query(sql, [...values, id], (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})

module.exports = router;