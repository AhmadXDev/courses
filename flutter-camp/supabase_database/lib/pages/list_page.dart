import 'package:flutter/material.dart';
import 'package:supabase_database/constants/spacing.dart';
import 'package:supabase_database/models/student.dart';
import 'package:supabase_database/services/database.dart';
import 'package:supabase_database/widgets/card_widget.dart';
import 'package:supabase_database/widgets/custom_button.dart';
import 'package:supabase_database/widgets/text_input.dart';
import 'package:loading_animation_widget/loading_animation_widget.dart';

class ListPage extends StatefulWidget {
  const ListPage({super.key});

  @override
  State<ListPage> createState() => ListPageState();
}

class ListPageState extends State<ListPage> {
  TextEditingController nameController = TextEditingController();
  TextEditingController ageController = TextEditingController();
  TextEditingController salaryController = TextEditingController();

  void clearData() {
    nameController.clear();
    ageController.clear();
    salaryController.clear();
  }

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.lightGreenAccent.shade200,
      ),
      body: FutureBuilder(
          future: Database().getData(),
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.done) {
              return ListView.builder(
                  itemCount: snapshot.data!.length,
                  itemBuilder: (context, index) {
                    Student item = snapshot.data![index];
                    return CardWidget(
                      student: item,
                    );
                  });
            } else if (snapshot.connectionState == ConnectionState.waiting) {
              return Center(
                  child: LoadingAnimationWidget.twistingDots(
                      leftDotColor: Colors.red,
                      rightDotColor: Colors.blue,
                      size: 48));
            } else {
              return const Center(
                child: Text("Error"),
              );
            }
          }),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          showDialog(
              context: context,
              builder: (context) {
                return Dialog(
                  child: Padding(
                    padding: const EdgeInsets.all(12.0),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        TextInput(
                            controller: nameController, labelText: "name"),
                        height24,
                        TextInput(controller: ageController, labelText: "age"),
                        height24,
                        TextInput(
                            controller: salaryController, labelText: "salary"),
                        height24,
                        CustomButton(
                            onPressed: () async {
                              try {
                                await Database().insertNewStudent(
                                    name: nameController.text,
                                    age: int.parse(ageController.text),
                                    salary:
                                        double.parse(salaryController.text));
                                if (context.mounted) {
                                  clearData();
                                  setState(() {});
                                  Navigator.pop(context);
                                }
                              } catch (e) {
                                if (context.mounted) {
                                  ScaffoldMessenger.of(context)
                                      .showSnackBar(SnackBar(
                                    content: Text(e.toString()),
                                    duration: const Duration(seconds: 10),
                                  ));
                                }
                              }
                            },
                            text: "insert")
                      ],
                    ),
                  ),
                );
              });
        },
        child: const Icon(Icons.add),
      ),
    );
  }
}
