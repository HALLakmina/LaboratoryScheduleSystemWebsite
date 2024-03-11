const express = require('express');
const router = express.Router();
const mysql = require('mysql');

const db =mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'laboratory_schedule_system'
})

router.get('/',(req, res)=>{
    const sql="SELECT * FROM practical_subject";
    db.query(sql, (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})
router.post('/', (req, res)=>{
    const sql="INSERT INTO practical_subject (Subject_cord, Subject, lecture_id, year) VALUE (?)"
    const values=[
        req.body.code,
        req.body.name,
        req.body.lectureID,
        req.body.year
    ]
    db.query(sql, [values], (err,data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})
router.delete('/:cord', (req, res)=>{
    const sql = "DELETE FROM practical_subject WHERE Subject_cord = ?";
    const id =req.params.cord;

    db.query(sql, [id], (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})
router.put('/:updateID', (req, res)=>{
    const sql = "UPDATE practical_subject SET Subject=?, lecture_id=?, year=? WHERE Subject_cord = ?";

    const values =[
        req.body.newName,
        req.body.newLecture,
        req.body.newYear,
    ]

    const cord = req.params.updateID;

    db.query(sql, [...values, cord], (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})

router.get('/subjectSearch/:cord',(req,res)=>{
    const sql = "SELECT * FROM practical_subject WHERE Subject_cord = ?"
    const id =req.params.cord;

    db.query(sql, [id], (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})

module.exports = router;