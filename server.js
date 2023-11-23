const express = require('express');
const bodyParser = require('body-parser');
const mongoose = require('mongoose');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcrypt');

const app = express();
const PORT = process.env.PORT || 3000;
const JWT_SECRET = 'yourSecretKey';

mongoose.connect('mongodb://localhost:27017/attendance_app', { useNewUrlParser: true, useUnifiedTopology: true });

const userSchema = new mongoose.Schema({
    // ... your existing fields
    role: { type: String, required: true },
    password: { type: String, required: true },
});

const User = mongoose.model('User', userSchema);

const authenticateUser = async (req, res, next) => {
    const token = req.header('Authorization');

    if (!token) {
        return res.status(401).json({ message: 'Unauthorized - Token missing' });
    }

    try {
        const decoded = jwt.verify(token, JWT_SECRET);
        req.user = decoded.user;
        next();
    } catch (error) {
        return res.status(401).json({ message: 'Unauthorized - Invalid token' });
    }
};

app.use(bodyParser.json());

// Registration route with parameterized query
app.post('/register', async (req, res) => {
    try {
        const { username, password, role } = req.body;

        // Use bcrypt to hash the password
        const hashedPassword = await bcrypt.hash(password, 10);

        // Use a parameterized query to insert the user data
        const newUser = new User({
            username,
            password: hashedPassword,
            role,
        });

        await newUser.save();

        res.json({ message: 'User registered successfully' });
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Internal Server Error' });
    }
});

// Login route with parameterized query
app.post('/login', async (req, res) => {
    try {
        const { username, password } = req.body;

        // Use a parameterized query to find the user by username
        const user = await User.findOne({ username });

        if (!user) {
            return res.status(401).json({ message: 'Invalid credentials' });
        }

        // Use bcrypt to compare the hashed password
        const passwordMatch = await bcrypt.compare(password, user.password);

        if (!passwordMatch) {
            return res.status(401).json({ message: 'Invalid credentials' });
        }

        // Create and send a JWT token upon successful login
        const token = jwt.sign({ user: { id: user._id } }, JWT_SECRET, { expiresIn: '1h' });
        res.json({ token });
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Internal Server Error' });
    }
});

app.listen(PORT, () => console.log(`Server is running on port ${PORT}`));

app.post('/submit', (req, res) => {
    // Assume 'input' is user input from a form
    const userInput = req.body.input;

    // Sanitize and validate the user input
    const sanitizedInput = sanitizeUserInput(userInput);

    // Process the sanitized input (e.g., save to the database)
    // ...

    res.send('Submission successful');
});

function sanitizeUserInput(input) {
    // Implement your sanitization logic here
    // For example, you can use a library like DOMPurify
    // or a custom function to escape HTML characters
    return input;
}

app.listen(PORT, () => console.log(`Server is running on port ${PORT}`));
