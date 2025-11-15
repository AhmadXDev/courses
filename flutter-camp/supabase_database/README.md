# supabase_database

AFTER DAY 12: 

Login Page: 

![image](https://github.com/user-attachments/assets/efcdf9ab-2c04-4965-b24d-cfe762a80a6c)

Signup Page: 

![image](https://github.com/user-attachments/assets/04bfc1c8-4217-4e34-87f0-1721e415f09b)

List Page: 

![image](https://github.com/user-attachments/assets/8bc62c1a-fde3-454a-8b66-cd223a0fbb96)

Adding a Student Card to List Page: 

![image](https://github.com/user-attachments/assets/dbe119f0-d059-4965-af76-9f9d07885d24)

Ediing a Student Card: 

![image](https://github.com/user-attachments/assets/0c55d806-391f-46ae-984a-fd043b2ff554)
![image](https://github.com/user-attachments/assets/558a5577-fc3e-4bbd-a729-cffe172b1cbf)

Note: it updates and finds the ListPageState because the context refrence is to the card widget context instead of the dialog context 

updated code: 

showDialog(context: context, builder: (BuildContext dialogContext) 
{

 context.findAncestorStateOfType<ListPageState>()?.setState(() {});
 
}

The argument is named dialogContext so it will take the context from the card widgit page isntead of the context passed in the builder and finds the List page in the tree.  


After Deleting a Student Card: 

![image](https://github.com/user-attachments/assets/d49e0d0e-a861-4beb-96d4-8e781c78ae03)
