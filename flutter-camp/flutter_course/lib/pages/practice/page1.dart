import 'package:flutter/material.dart';
import 'package:flutter_course/pages/weeks/week1/day5/page2.dart';

class Page1 extends StatelessWidget {
  const Page1({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(
          backgroundColor: Colors.green,
          title: const Text("Welcome"),
        ),
        body: Padding(
            padding: const EdgeInsets.all(8),
            child: Container(
              width: 300,
              height: 300,
              color: Colors.blue.shade300,
              padding: const EdgeInsets.only(top: 20, left: 0),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  const Text(
                    "Title",
                    style: TextStyle(
                        color: Color.fromARGB(255, 219, 30, 30),
                        fontSize: 40,
                        fontWeight: FontWeight.bold),
                  ),
                  const Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Icon(Icons.face),
                      Icon(Icons.face_2),
                      Icon(Icons.face),
                      Icon(Icons.face_2),
                      Icon(Icons.face),
                    ],
                  ),
                  const Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Icon(Icons.face),
                      Icon(Icons.face_2),
                      Icon(Icons.face),
                      Icon(Icons.face_2),
                      Icon(Icons.face),
                    ],
                  ),
                  ElevatedButton(
                    onPressed: () {
                      Navigator.push(
                          context,
                          MaterialPageRoute(
                              builder: (context) => const Page2()));
                    },
                    child: const Text("😆"),
                  )
                ],
              ),
            )));
  }
}
