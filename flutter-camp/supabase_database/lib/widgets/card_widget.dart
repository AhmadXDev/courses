import 'package:flutter/material.dart';
import 'package:supabase_database/constants/spacing.dart';
import 'package:supabase_database/models/student.dart';
import 'package:supabase_database/pages/list_page.dart';
import 'package:supabase_database/services/database.dart';
import 'package:supabase_database/widgets/custom_button.dart';
import 'package:supabase_database/widgets/text_input.dart';

class CardWidget extends StatefulWidget {
  final Student student;
  const CardWidget({super.key, required this.student});

  @override
  State<CardWidget> createState() => _CardWidgetState();
}

class _CardWidgetState extends State<CardWidget> {
  TextEditingController nameController = TextEditingController();
  TextEditingController salaryController = TextEditingController();

  void clearData() {
    nameController.clear();
    salaryController.clear();
  }

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
          padding: const EdgeInsets.all(8.0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              SizedBox(
                child: Row(
                  children: [
                    Text("${widget.student.id} )"),
                    width8,
                    Text(widget.student.name.toString()),
                  ],
                ),
              ),
              Text(widget.student.age.toString()),
              Text(widget.student.salary.toString()),
              Column(
                children: [
                  InkWell(
                    onTap: () async {
                      await Database().deleteUser(id: widget.student.id!);
                      if (context.mounted) {
                        context
                            .findAncestorStateOfType<ListPageState>()
                            ?.setState(() {});
                      }
                    },
                    child: Icon(
                      Icons.delete,
                      size: 20,
                      color: Colors.red.shade300,
                    ),
                  ),
                  height12,
                  InkWell(
                    onTap: () {
                      nameController.text = widget.student.name!;
                      salaryController.text = widget.student.salary.toString();
                      showDialog(
                        context: context,
                        builder: (BuildContext dialogContext) {
                          return Dialog(
                            child: Padding(
                              padding: const EdgeInsets.all(20.0),
                              child: Column(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  height24,
                                  TextInput(
                                      controller: nameController,
                                      labelText: "name"),
                                  height24,
                                  TextInput(
                                      controller: salaryController,
                                      labelText: "salary"),
                                  height24,
                                  CustomButton(
                                    onPressed: () async {
                                      try {
                                        await Database().updateUser(
                                          name: nameController.text,
                                          id: widget.student.id!,
                                          salary: double.parse(
                                              salaryController.text),
                                        );
                                        if (context.mounted) {
                                          clearData();
                                          context
                                              .findAncestorStateOfType<
                                                  ListPageState>()
                                              ?.setState(() {});
                                          Navigator.pop(dialogContext);
                                        }
                                      } catch (e) {
                                        ScaffoldMessenger.of(context)
                                            .showSnackBar(
                                          SnackBar(content: Text(e.toString())),
                                        );
                                      }
                                    },
                                    text: "edit student",
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      );
                    },
                    child: const Icon(
                      Icons.edit,
                      size: 20,
                    ),
                  )
                ],
              )
            ],
          )),
    );
  }
}
