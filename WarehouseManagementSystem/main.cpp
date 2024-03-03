#include<iostream>
#include<conio.h>
using namespace std;

class warehousing
{
private:
    string nameItem;
    int codeItem;
//    int priceItem;
//    int position;
public:
    void createItem(string nameItem, int codeItem);
//    void pushItem(string nameItem, int priceItem);
    void pushItem();
    void popItem();
    void display();
    void peekItem();
    void changeItem(string nameItem, int priceItem, int position);
    void destroyItem();
};


// create linked list node
struct dataItem{
    string nameItem;
    int codeItem;

    //pointer
    dataItem *prev;
    dataItem *next;
};

//global variable
dataItem *head, *tail, *cur, *newNode, *del;
int maxItem = 5;

void warehousing::createItem( string nameItem, int codeItem ){
    head = new dataItem();
    head->nameItem = nameItem;
    head->codeItem = codeItem;
    head->prev = NULL;
    head->next = NULL;
    tail = head;
}

int countItem()
{
    if( head == NULL ){
        return 0;
    }else{
        int nominal = 0; // init
        cur = head;

        while( cur != NULL ){
            cur = cur->next;
            nominal++;
        }
        return nominal;
    }
}

bool isFullItem()
{
    if( countItem() == maxItem ){
        return true;
    }else{
        return false;
    }
}

bool isEmptyItem()
{
    if( countItem() == 0 ){
        return true;
    }else{
        return false;
    }
}


//void warehousing :: pushItem( string nameItem, int priceItem ){
void warehousing :: pushItem(){


	cout<<" Enter Item name\t: ";
    cin>>nameItem;
    cout<<" Enter item code\t: ";
    cin>>codeItem;


    if( isFullItem() ){
        cout << "The Warehouse is Full" << endl;
    }else{
        if( isEmptyItem() ){
            createItem(nameItem, codeItem);
        }else{
            newNode = new dataItem();
            newNode->nameItem = nameItem;
            newNode->codeItem = codeItem;
            newNode->prev = tail;
            tail->next = newNode;
            newNode->next = NULL;
            tail = newNode;
        }
    }
}

void warehousing :: popItem(){
//    del = tail;
//    tail = tail->prev;
//    tail->next = NULL;
//    delete del;
    //-----------
    if(head==NULL)
		{cout<<" Warehouse is empty\n";
		getch();}
	else
	{
		cur=head;
		head=head->next;
		cout<<" Item code "<<cur->codeItem<<" removed\n";
		delete(cur);
		getch();
	}
}


void warehousing :: display(){
    if( isEmptyItem() ){
        cout << "Warehouse is empty\t" << endl;
        getch();
    }else{
        cout << "Warehouse items (item name, item code) : " << endl;
        cur = tail;

        while( cur != NULL ){
            cout << cur->nameItem << " - " << cur->codeItem << endl;
            cur = cur->prev;

        }
        getch();
    }
}

void warehousing :: peekItem(){

    int find1;
    int found = 0;
    cur = tail;

    if(tail != NULL)
    {
        cout<<"\n Enter item code\t: ";
        cin>>find1;

        while(cur!=NULL)
        {
            cur->codeItem;
            if(find1==cur->codeItem)
            {
                cout<<"\n\n>>>   Item founded   <<<"<<endl;
                cout<<" Item name\t: "<<cur->nameItem<<endl;
                cout<<" ------------------------------"<<endl;
                cout<<"\n\n";
              found=1;
            }
            cur = cur->prev;
        }
        if(found==0)
        {
            cout<<" Item not found";
        }
    }
    else cout<<" Warehouse is empty";
    getch();

    //------------------------------
//    if( isEmptyItem() ){
//        cout << "Warehouse is empty" << endl;
//    }else{
//        cur = tail;
//
//        while( cur != NULL ){
//            if( codeItem == cur->codeItem )
//            cur = cur->prev;
//
//        }
//
//        int no = 1;
//        cur = tail;
//        while( no < position ){
//            cur = cur->prev;
//            no++;
//        }
//    }
//    cout << "Data Item position ke-" << position << " :" << cur->nameItem << " - " << cur->codeItem << endl;

}

void warehousing :: changeItem(string nameItem, int priceItem, int position){
    if( isEmptyItem() ){
        cout << "Stack empty" << endl;
    }else{
        int no = 1;
        cur = tail;
        while( no < position ){
            cur = cur->prev;
            no++;
        }
    }
    cur->nameItem = nameItem;
    cur->codeItem = codeItem;
}

void warehousing :: destroyItem(){
    cur = head;
    while( cur != NULL ){
        del = cur;
        head = head->next;
        delete del;
        cur = cur->next;
    }
}


int main()
{
	warehousing w;
	int ch,nr,isi,operatemenu;
	while(1)
	{
	    operatemenu=0;
		system("cls");
	    cout<<"\n\n=================================================";
	    cout<<"\n               Warehouse System                  ";
		cout<<"\n_________________________________________________";
		cout<<"\n                       Menu                    ";
        cout<<"\n=================================================";
		cout<<"\n 1.Input item";
		cout<<"\n 2.Display Warehouse item";
		cout<<"\n 3.Find item";
		cout<<"\n 4.Remove item";
		cout<<"\n 5.Change item";
		cout<<"\n 6.Loadout Warehouse";
		cout<<"\n 7.Exit Program";
		while(operatemenu<1||operatemenu>7){
			cout<<"\n\n Enter command (1-7): ";
			cin>>operatemenu;
			if(operatemenu<1||operatemenu>7){
				cout<<"Wrong Input, Please enter a new one!";
				operatemenu=0;
				cin.clear();
				cin.ignore(100, '\n');
			}
			else{
				ch=operatemenu;	
						
			}
		}

		cout<<"\n=================================================";
		cout<<"\n";
		switch(ch)
		{
			case 1:
			    w.pushItem();
                break;
			case 2:
			    w.display();
			    break;
            case 3:
                w.peekItem();
                break;
            case 4:
                w.popItem();
                break;
//			case 5:
//			    w.change();
//                break;
            case 6:
			    w.destroyItem();
                break;

			case 7:
			    exit(0);
                break;
			default:
			    cout<<"Wrong Input, Please enter a new one! (press any key)";
			    getch();
		}
	}
	return 0;
}