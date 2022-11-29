db.createUser (
    {
        user : "app",
        pwd : "pwdXXX1234",
        roles : [
            {
                role : "readWrite",
                db : "initmongodb"
            }
        ]
    }
)
