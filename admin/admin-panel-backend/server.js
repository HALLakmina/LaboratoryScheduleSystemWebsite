const express = require('express');
const mysql = require('mysql');
const cors = require('cors');
const timetable = require('./routers/timetableDate');
const lecture = require('./routers/lectureData');
const subject = require('./routers/subjectData');
const app = express();

app.use(cors());
app.use(express.json());

const db =mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'laboratory_schedule_system'
})

app.get('/', (re, res)=>{
    return res.json("From Backend Side");
})

//==========Login==========//

app.get('/login', (req, res)=>{
    const sql = "SELECT * FROM admin_details WHERE user_name = ? AND password = ?";
    const values= [
        req.body.userName,
        req.body.password,
    ]

    db.query(sql, [...values], (err, data)=>{
        if(err) return res.json(err);
        if(data.length>0)
        {
            return res.json(data, "Login Successful");    
        }else{
            return res.json("Ypu are not Admin");
        }
        
    })
})

//==========Lecture==========//

app.use('/lecture_details', lecture);

//==========Admin==========//

app.get('/admin_details', (req, res)=>{
    const sql="SELECT * FROM admin_details";
    db.query(sql, (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})

app.post('/add_admin_details', (req, res) =>{
    const sql= "INSERT INTO admin_details (full_name, user_name, nic, mobile_number, email, password) VALUE (?)";
    const values =[
        req.body.adminName,
        req.body.userName,
        req.body.adminID,
        req.body.mobileNo,
        req.body.email,
        req.body.password,
    ]
    db.query(sql, [values], (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})

app.delete('/remove_admin_details/:id', (req, res)=>{
    const sql = "DELETE FROM admin_details WHERE nic = ?"
    const id = req.params.id;

    db.query(sql, [id], (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})

app.get('/search_admin_details/:id', (req, tes)=>{
    const sql="SELECT * FROM admin_details WHERE NIC = ?";
    const id = res.params.id;

    db.query(sql ,[id], (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})

app.put('/update_admin_details/:updateAdminID', (req, res)=>{
    const sql = "UPDATE admin_details SET full_name= ?, user_name=?, mobile_number=?, email=?, password=? WHERE nic = ?";
    
    const values =[
        req.body.updateAdminName,
        req.body.updateUserName,
        req.body.updateMobileNo,
        req.body.updateEmail,
        req.body.updatePassword
    ]
    const id = req.params.updateAdminID;
    db.query(sql, [...values, id], (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})

//==========Subject==========//

app.use('/practical_subject', subject);

//==========News==========//

app.get('/news', (req, res)=>{
    const sql="SELECT * FROM news";
    db.query(sql,(err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})

app.post('/add_news', (req, res)=>{
    const sql="INSERT INTO news (title, image, description, place, time, date) VALUE (?)"
    const values = [
        req.body.title,
        req.body.imageName,
        req.body.description,
        req.body.location,
        req.body.time,
        req.body.date,
    ];

    db.query(sql, [values], (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})

app.delete('/remove_news/:id', (req, res)=>{
    const sql="DELETE FROM news WHERE id=?";
    const id = req.params.id;

    db.query(sql, [id], (err, data)=>{
        if(err)return res.json(err);
        return res.json(data);
    })
})
app.put('/update_news/:id', (req, res)=>{
    const sql="UPDATE news SET title=?, image=?, description=?, place=?, time=?, date=? WHERE id= ?"
    const values=[
        req.body.title,
        req.body.newImageName,
        req.body.description,
        req.body.location,
        req.body.time,
        req.body.date,
    ]
    const id = req.params.id;

    db.query(sql, [...values, id], (err, data)=>{
        if (err) return res.json(err);
        return res.json(data);
    })
})

//==========Timetable==========//

app.use('/timetable', timetable);



app.listen(8081, ()=>{
    console.log("listening");
})