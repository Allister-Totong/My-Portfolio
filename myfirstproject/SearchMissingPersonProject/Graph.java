import java.util.*;


public class Graph {
    
    private int Vertices;       
	private LinkedList<Integer> adjlist[];      
    int i; 
	public Graph(int countV)   
	{     
		Vertices = countV;   
		adjlist = new LinkedList[countV];   
		for (i=0; i<countV; ++i)   
			adjlist[i] = new LinkedList();   
	}   
	void addEdge(int v1, int v2)
	{
		 adjlist[v1].add(v2);   
	}
    private int founded;
    public void SetFoundIn(int current)
    {
        this.founded=current;
    }
    public int GetFoundIn()
    { 
        return this.founded;
    }
    public int GetVertice()
    { 
        return Vertices;
    }
    public LinkedList<Integer> Getadj(int i)
    { 
        return adjlist[i];
    }
}
