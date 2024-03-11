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
    const sql = 'SELECT * FROM timetable';
    db.query(sql, (err,data)=>{
        if(err) return res.json(err);
        return res.json(data);
    })
})

router.get('/allDetails',(req, res)=>{
    const sql = 'SELECT timetable.id,timetable.practical_group,timetable.Action,practical_subject.Subject_cord,practical_subject.Subject,practical_subject.year,lecture_details.full_name FROM timetable INNER JOIN practical_subject ON timetable.Subject_cord = practical_subject.Subject_cord INNER JOIN lecture_details ON practical_subject.lecture_id = lecture_details.lecture_id';
    db.query(sql, (err,data)=>{
        if(err) return res.json(err);
        return res.json(data);
    })
})

router.put('/:id', (req, res)=>{
    const sql ="UPDATE timetable SET Subject_cord=?, practical_group=?, Action=? WHERE id = ?";

    const values =[
        req.body.newSubjectCord,
        req.body.newGroup,
        req.body.newActin
    ];
    const cell = req.params.id;

    db.query(sql, [...values, cell], (err, data)=>{
        if(err) return res.json(err);
        return res.json(data);
    })
})

module.exports = router;