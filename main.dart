import 'package:flutter/material.dart';

void main() {
  runApp(const StudentManagementApp());
}

class StudentManagementApp extends StatelessWidget {
  const StudentManagementApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Student Management',
      theme: ThemeData(
        primaryColor: const Color(0xFF800000), // Maroon
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF800000),
          primary: const Color(0xFF800000), // Maroon
          secondary: const Color(0xFFFFD700), // Gold
          surface: Colors.white,
          background: const Color(0xFFFFF8E7), // Light cream background
        ),
        appBarTheme: const AppBarTheme(
          backgroundColor: Color(0xFF800000), // Maroon
          foregroundColor: Color(0xFFFFD700), // Gold text
          elevation: 0,
          centerTitle: true,
          titleTextStyle: TextStyle(
            color: Color(0xFFFFD700),
            fontSize: 22,
            fontWeight: FontWeight.bold,
            letterSpacing: 1.2,
          ),
        ),
        cardTheme: CardThemeData(
          elevation: 3,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
            side: BorderSide(color: const Color(0xFF800000).withValues(alpha: 0.2), width: 1),
          ),
          color: Colors.white,
        ),
        floatingActionButtonTheme: const FloatingActionButtonThemeData(
          backgroundColor: Color(0xFFFFD700), // Gold
          foregroundColor: Color(0xFF800000), // Maroon icon
          elevation: 6,
        ),
        inputDecorationTheme: InputDecorationTheme(
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8),
            borderSide: const BorderSide(color: Color(0xFF800000)),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8),
            borderSide: const BorderSide(color: Color(0xFF800000), width: 2),
          ),
          labelStyle: const TextStyle(color: Color(0xFF800000)),
          prefixIconColor: Color(0xFF800000),
        ),
        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xFF800000), // Maroon
            foregroundColor: const Color(0xFFFFD700), // Gold text
            elevation: 2,
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(8),
            ),
          ),
        ),
        textButtonTheme: TextButtonThemeData(
          style: TextButton.styleFrom(
            foregroundColor: const Color(0xFF800000), // Maroon
          ),
        ),
        useMaterial3: true,
      ),
      home: const StudentListScreen(),
    );
  }
}

class Student {
  String id;
  String name;
  String email;
  String course;
  String phoneNumber;

  Student({
    required this.id,
    required this.name,
    required this.email,
    required this.course,
    required this.phoneNumber,
  });
}

class StudentListScreen extends StatefulWidget {
  const StudentListScreen({super.key});

  @override
  State<StudentListScreen> createState() => _StudentListScreenState();
}

class _StudentListScreenState extends State<StudentListScreen> {
  List<Student> students = [];
  int _nextId = 1;

  void _addStudent(Student student) {
    setState(() {
      students.add(student);
    });
  }

  void _updateStudent(int index, Student student) {
    setState(() {
      students[index] = student;
    });
  }

  void _deleteStudent(int index) {
    setState(() {
      students.removeAt(index);
    });
  }

  void _showStudentForm({Student? student, int? index}) {
    showDialog(
      context: context,
      builder: (context) => StudentFormDialog(
        student: student,
        onSave: (updatedStudent) {
          if (index != null) {
            _updateStudent(index, updatedStudent);
          } else {
            updatedStudent.id = 'STU${_nextId.toString().padLeft(3, '0')}';
            _nextId++;
            _addStudent(updatedStudent);
          }
        },
      ),
    );
  }

  void _confirmDelete(int index) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Confirm Delete'),
        content: Text('Are you sure you want to delete ${students[index].name}?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cancel'),
          ),
          TextButton(
            onPressed: () {
              _deleteStudent(index);
              Navigator.pop(context);
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Student deleted successfully')),
              );
            },
            child: const Text('Delete', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('STUDENT MANAGEMENT'),
        elevation: 2,
      ),
      backgroundColor: const Color(0xFFFFF8E7), // Light cream background
      body: students.isEmpty
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.school_outlined, size: 80, color: Colors.grey[400]),
                  const SizedBox(height: 16),
                  Text(
                    'No students enrolled yet',
                    style: TextStyle(fontSize: 18, color: Colors.grey[600]),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Tap + to add a new student',
                    style: TextStyle(color: Colors.grey[500]),
                  ),
                ],
              ),
            )
          : ListView.builder(
              itemCount: students.length,
              padding: const EdgeInsets.all(8),
              itemBuilder: (context, index) {
                final student = students[index];
                return Card(
                  elevation: 2,
                  margin: const EdgeInsets.symmetric(vertical: 4, horizontal: 8),
                  child: Container(
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(12),
                      gradient: LinearGradient(
                        colors: [
                          Colors.white,
                          const Color(0xFFFFD700).withOpacity(0.05),
                        ],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                    ),
                    child: ListTile(
                      contentPadding: const EdgeInsets.all(12),
                      leading: CircleAvatar(
                        backgroundColor: const Color(0xFF800000),
                        child: Text(
                          student.name[0].toUpperCase(),
                          style: const TextStyle(
                            color: Color(0xFFFFD700),
                            fontWeight: FontWeight.bold,
                            fontSize: 20,
                          ),
                        ),
                      ),
                      title: Text(
                        student.name,
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF800000),
                          fontSize: 18,
                        ),
                      ),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const SizedBox(height: 4),
                          Text(
                            'ID: ${student.id}',
                            style: const TextStyle(
                              color: Color(0xFF800000),
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                          Text('Course: ${student.course}'),
                          Text('Email: ${student.email}'),
                        ],
                      ),
                      isThreeLine: true,
                      trailing: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          IconButton(
                            icon: const Icon(Icons.edit),
                            color: const Color(0xFFFFD700),
                            style: IconButton.styleFrom(
                              backgroundColor: const Color(0xFF800000),
                            ),
                            onPressed: () => _showStudentForm(
                              student: student,
                              index: index,
                            ),
                          ),
                          const SizedBox(width: 4),
                          IconButton(
                            icon: const Icon(Icons.delete),
                            color: Colors.white,
                            style: IconButton.styleFrom(
                              backgroundColor: Colors.red[700],
                            ),
                            onPressed: () => _confirmDelete(index),
                          ),
                        ],
                      ),
                    ),
                  ),
                );
              },
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showStudentForm(),
        child: const Icon(Icons.add),
      ),
    );
  }
}

class StudentFormDialog extends StatefulWidget {
  final Student? student;
  final Function(Student) onSave;

  const StudentFormDialog({
    super.key,
    this.student,
    required this.onSave,
  });

  @override
  State<StudentFormDialog> createState() => _StudentFormDialogState();
}

class _StudentFormDialogState extends State<StudentFormDialog> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _emailController;
  late TextEditingController _courseController;
  late TextEditingController _phoneController;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.student?.name ?? '');
    _emailController = TextEditingController(text: widget.student?.email ?? '');
    _courseController = TextEditingController(text: widget.student?.course ?? '');
    _phoneController = TextEditingController(text: widget.student?.phoneNumber ?? '');
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _courseController.dispose();
    _phoneController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side: const BorderSide(color: Color(0xFF800000), width: 2),
      ),
      title: Row(
        children: [
          Icon(
            widget.student == null ? Icons.person_add : Icons.edit,
            color: const Color(0xFF800000),
          ),
          const SizedBox(width: 8),
          Text(
            widget.student == null ? 'Add Student' : 'Edit Student',
            style: const TextStyle(
              color: Color(0xFF800000),
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
      content: SingleChildScrollView(
        child: Form(
          key: _formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextFormField(
                controller: _nameController,
                decoration: const InputDecoration(
                  labelText: 'Name',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.person),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter student name';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _emailController,
                decoration: const InputDecoration(
                  labelText: 'Email',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.email),
                ),
                keyboardType: TextInputType.emailAddress,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter email';
                  }
                  if (!value.contains('@')) {
                    return 'Please enter a valid email';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _courseController,
                decoration: const InputDecoration(
                  labelText: 'Course',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.book),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter course';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _phoneController,
                decoration: const InputDecoration(
                  labelText: 'Phone Number',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.phone),
                ),
                keyboardType: TextInputType.phone,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter phone number';
                  }
                  return null;
                },
              ),
            ],
          ),
        ),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: const Text('Cancel'),
        ),
        ElevatedButton(
          onPressed: () {
            if (_formKey.currentState!.validate()) {
              final student = Student(
                id: widget.student?.id ?? '',
                name: _nameController.text,
                email: _emailController.text,
                course: _courseController.text,
                phoneNumber: _phoneController.text,
              );
              widget.onSave(student);
              Navigator.pop(context);
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text(
                    widget.student == null
                        ? 'Student added successfully'
                        : 'Student updated successfully',
                  ),
                ),
              );
            }
          },
          child: const Text('Save'),
        ),
      ],
    );
  }
}